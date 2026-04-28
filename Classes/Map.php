<?php

declare(strict_types=1);

namespace Iresults\Collection;

use ArrayAccess;
use InvalidArgumentException;
use Iresults\Collection\Traits\ReduceTrait;
use Iresults\Collection\Transformer\Partition as PartitionTransformer;
use Iterator;
use UnexpectedValueException;

use function count;
use function current;
use function gettype;
use function is_array;
use function is_object;
use function is_scalar;
use function is_string;
use function key;
use function next;
use function reset;
use function spl_object_hash;
use function sprintf;
use function uasort;

/**
 * @template K of string|int|float|bool|object
 * @template V
 *
 * @implements MapInterface<K,V>
 * @implements Iterator<K,V>
 * @implements ArrayAccess<K,V>
 */
class Map implements Iterator, MapInterface, ArrayAccess
{
    use ReduceTrait;

    /**
     * Map of the object hash to the key object
     *
     * @var array<string,K>
     */
    private array $hashToKeyMap = [];

    /**
     * Map of the object hash to the value object
     *
     * @var array<string,V>
     */
    private array $hashToValueMap = [];

    /**
     * @param Pair<K,V>|array{0:K,1:V} $objects
     */
    final public function __construct(array|Pair ...$objects)
    {
        foreach ($objects as $objectAndValue) {
            $this->assertPair($objectAndValue);
            if ($objectAndValue instanceof Pair) {
                $this->set($objectAndValue->key, $objectAndValue->value);
            } else {
                $this->set($objectAndValue[0], $objectAndValue[1]);
            }
        }
    }

    public function getValues(): array
    {
        return $this->hashToValueMap;
    }

    public function getKeys(): array
    {
        return $this->hashToKeyMap;
    }

    /**
     * @return V|null
     */
    public function current(): mixed
    {
        $currentKey = $this->hashKey();
        if (null !== $currentKey) {
            return $this->hashToValueMap[$currentKey] ?? null;
        }

        return null;
    }

    public function next(): void
    {
        next($this->hashToKeyMap);
    }

    /**
     * @return false|string|float|object|bool|int
     */
    public function key(): string|float|object|bool|int
    {
        return current($this->hashToKeyMap);
    }

    private function hashKey(): ?string
    {
        return key($this->hashToKeyMap);
    }

    public function valid(): bool
    {
        return false !== current($this->hashToKeyMap) || null !== key($this->hashToKeyMap);
    }

    public function rewind(): void
    {
        reset($this->hashToKeyMap);
        reset($this->hashToValueMap);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->hashToKeyMap[$this->hash($offset)]);
    }

    public function exists(mixed $key): bool
    {
        return isset($this->hashToKeyMap[$this->hash($key)]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function get(mixed $key): mixed
    {
        if (!$this->exists($key)) {
            return null;
        }

        return $this->hashToValueMap[$this->hash($key)];
    }

    public function getByHash(string $hash): mixed
    {
        return $this->hashToValueMap[$hash] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            throw new InvalidArgumentException(
                static::class . ' does not support array appending'
            );
        }

        $this->set($offset, $value);
    }

    public function set(mixed $key, mixed $value): void
    {
        $hash = $this->hash($key);
        $this->hashToKeyMap[$hash] = $key;
        $this->hashToValueMap[$hash] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function remove(mixed $key): mixed
    {
        $value = $this->get($key);
        $hash = $this->hash($key);
        unset($this->hashToKeyMap[$hash]);
        unset($this->hashToValueMap[$hash]);

        return $value;
    }

    public function count(): int
    {
        return count($this->hashToKeyMap);
    }

    public function find(callable $callback): mixed
    {
        foreach ($this as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    public function findPair(callable $callback): ?Pair
    {
        foreach ($this as $key => $value) {
            $pair = new Pair($key, $value);
            if ($callback($pair)) {
                return $pair;
            }
        }

        return null;
    }

    public function sort(callable $callback): static
    {
        $values = $this->hashToValueMap;
        uasort($values, $callback);

        /** @var static<K,V> $result */
        $result = new static();
        foreach ($values as $hash => $value) {
            $result->offsetSet($this->hashToKeyMap[$hash], $value);
        }

        return $result;
    }

    public function sortByKey(callable $callback): static
    {
        $keyObjectMap = $this->hashToKeyMap;
        uasort($keyObjectMap, $callback);

        /** @var static<K,V> $result */
        $result = new static();
        foreach ($keyObjectMap as $hash => $key) {
            $result->offsetSet($key, $this->hashToValueMap[$hash]);
        }

        return $result;
    }

    public function filter(callable $callback): static
    {
        $pairs = [];
        foreach ($this as $keyObject => $value) {
            if ($callback($value, $keyObject)) {
                $pairs[] = new Pair($keyObject, $value);
            }
        }

        /** @var static<K,V> $result */
        $result = new static(...$pairs);

        return $result;
    }

    /**
     * @template R
     *
     * @param callable(V, K): R $callback Callback to apply
     *
     * @return MapInterface<K,R>
     */
    public function map(callable $callback): MapInterface
    {
        $pairs = [];
        foreach ($this as $key => $value) {
            $callbackResult = $callback($value, $key);
            $pairs[] = new Pair($key, $callbackResult);
        }

        /** @var Map<K,R> $result */
        $result = new self(...$pairs);

        return $result;
    }

    public function filterMap(callable $callback): static
    {
        $pairs = [];
        foreach ($this as $key => $value) {
            $callbackResult = $callback($value, $key);
            if (null !== $callbackResult) {
                $pairs[] = new Pair($key, $callbackResult);
            }
        }

        /** @var static<K,V> $result */
        $result = new static(...$pairs);

        return $result;
    }

    /**
     * @template R
     *
     * @param callable(V, K): R $callback
     *
     * @return MapInterface<R, CollectionInterface<V>>
     */
    public function partition(callable $callback): MapInterface
    {
        // @phpstan-ignore return.type,argument.templateType
        return (new PartitionTransformer())->apply(
            $this,
            $callback,
            Collection::class
        );
    }

    /**
     * @param string|int|float|bool|object $variable
     */
    private function hash(mixed $variable): string
    {
        $prefix = static::class . '-';
        if (is_string($variable)) {
            return $prefix . $variable;
        }
        if (is_scalar($variable)) {
            return $prefix . (string) $variable;
        }
        if (is_object($variable)) {
            return $prefix . spl_object_hash($variable);
        }
        throw new UnexpectedValueException(
            sprintf('Can not create hash for variable of type "%s"', gettype($variable)),
            1442825536
        );
    }

    /**
     * @param array<mixed,mixed>|Pair<mixed,mixed> $objectAndValue
     *
     * @phpstan-assert Pair<K,V>|array{0:K,1:V} $objectAndValue
     */
    private static function assertPair($objectAndValue): void
    {
        if ($objectAndValue instanceof Pair) {
            return;
        }
        if (!is_array($objectAndValue)) {
            throw new InvalidArgumentException(
                'Constructor argument must be an array of paris',
                1442827041
            );
        }
        if (!array_key_exists(0, $objectAndValue) || !array_key_exists(1, $objectAndValue)) {
            throw new InvalidArgumentException(
                'Constructor argument must be an array of pairs',
                1442827041
            );
        }
    }
}
