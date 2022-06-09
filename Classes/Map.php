<?php
declare(strict_types=1);

namespace Iresults\Collection;

use ArrayIterator;
use InvalidArgumentException;
use Iresults\Collection\Traits\FilterMapTrait;
use Iresults\Collection\Traits\FilterTrait;
use Iresults\Collection\Traits\MapTrait;
use Iresults\Collection\Traits\PartitionTrait;
use Iresults\Collection\Traits\ReduceTrait;
use Iterator;
use UnexpectedValueException;
use function is_scalar;

/**
 * Object-to-object data store
 *
 * @template K
 * @template V
 * @implements MapInterface<K,V>
 */
class Map implements Iterator, MapInterface
{
    use PartitionTrait;
    use ReduceTrait;
    use FilterTrait;
    use MapTrait;
    use FilterMapTrait;

    /**
     * Map of the object hash to the key object
     *
     * @var array<string,K>
     */
    private array $hashToKeyObjectMap = [];

    /**
     * Map of the object hash to the value object
     *
     * @var array
     */
    private array $hashToValueMap = [];

    /**
     * Map constructor
     *
     * @param array $objects
     */
    public function __construct(array $objects = [])
    {
        foreach ($objects as $objectAndValue) {
            $this->assertPair($objectAndValue);
            $this->offsetSet($objectAndValue[0], $objectAndValue[1]);
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getArrayCopy());
    }

    public static function withPairs(...$pairs): MapInterface
    {
        foreach ($pairs as $pair) {
            static::assertPair($pair);
        }

        return new static($pairs);
    }

    public function getArrayCopy(): array
    {
        return $this->hashToValueMap;
    }

    public function getKeys(): array
    {
        return $this->hashToKeyObjectMap;
    }

    public function current()
    {
        $currentKey = $this->hashKey();
        if (isset($this->hashToValueMap[$currentKey])) {
            return $this->hashToValueMap[$currentKey];
        }

        return null;
    }

    public function next()
    {
        next($this->hashToKeyObjectMap);
    }

    public function key()
    {
        return current($this->hashToKeyObjectMap);
    }

    public function hashKey()
    {
        return key($this->hashToKeyObjectMap);
    }

    public function valid(): bool
    {
        return (current($this->hashToKeyObjectMap) !== false);
    }

    public function rewind()
    {
        reset($this->hashToKeyObjectMap);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->hashToKeyObjectMap[$this->hash($offset)]);
    }

    public function exists($keyObject): bool
    {
        return $this->offsetExists($keyObject);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        return $this->hashToValueMap[$this->hash($offset)];
    }

    public function get($keyObject)
    {
        return $this->offsetGet($keyObject);
    }

    public function offsetSet($offset, $value)
    {
        $hash = $this->hash($offset);
        $this->hashToKeyObjectMap[$hash] = $offset;
        $this->hashToValueMap[$hash] = $value;
    }

    public function set($keyObject, $value)
    {
        $this->offsetSet($keyObject, $value);
    }

    public function offsetUnset($offset)
    {
        $hash = $this->hash($offset);
        unset($this->hashToKeyObjectMap[$hash]);
        unset($this->hashToValueMap[$hash]);
    }

    public function remove($keyObject)
    {
        $value = $this->get($keyObject);
        $this->offsetUnset($keyObject);

        return $value;
    }

    public function count(): int
    {
        return count($this->hashToKeyObjectMap);
    }

    public function find(callable $callback)
    {
        foreach ($this as $value) {
            if ($callback($value)) {
                return $value;
            }
        }

        return null;
    }

    public function findPair(callable $callback): ?Pair
    {
        foreach ($this as $keyObject => $value) {
            $pair = new Pair($keyObject, $value);
            if ($callback($pair)) {
                return $pair;
            }
        }

        return null;
    }

    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->getArrayCopy());
    }

    public function sort(callable $callback): CollectionInterface
    {
        $values = $this->hashToValueMap;
        uasort($values, $callback);

        $result = new static();
        foreach ($values as $hash => $value) {
            $result->offsetSet($this->hashToKeyObjectMap[$hash], $value);
        }

        return $result;
    }

    public function ksort(callable $callback): CollectionInterface
    {
        $keyObjectMap = $this->hashToKeyObjectMap;
        uasort($keyObjectMap, $callback);

        $result = new static();
        foreach ($keyObjectMap as $hash => $key) {
            $result->offsetSet($key, $this->hashToValueMap[$hash]);
        }

        return $result;
    }

    /**
     * @param string|object $variable
     * @return string
     */
    protected function hash($variable): string
    {
        if (is_string($variable)) {
            return $variable;
        }
        if (is_scalar($variable)) {
            return (string)$variable;
        }
        if (is_object($variable)) {
            return spl_object_hash($variable);
        }
        throw new UnexpectedValueException(
            sprintf('Can not create hash for variable of type "%s"', gettype($variable)),
            1442825536
        );
    }

    /**
     * @param array|Pair $objectAndValue
     * @return void
     */
    private static function assertPair($objectAndValue)
    {
        if ($objectAndValue instanceof Pair) {
            return;
        }
        if (!is_array($objectAndValue)) {
            throw new InvalidArgumentException('Constructor argument must be an array of arrays', 1442827041);
        }
        if (!isset($objectAndValue[0]) || count($objectAndValue) < 2) {
            throw new InvalidArgumentException('Constructor argument must be an array of arrays', 1442827041);
        }
    }
}
