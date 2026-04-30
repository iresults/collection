<?php

declare(strict_types=1);

namespace Iresults\Collection;

use ArrayIterator;
use BadMethodCallException;
use InvalidArgumentException;
use Iresults\Collection\Traits\ReduceTrait;
use Iresults\Collection\Transformer\Partition as PartitionTransformer;
use Iresults\Collection\Utility\TypeUtility;
use IteratorAggregate;

/**
 * Immutable list of items
 *
 * @template V
 *
 * @implements IteratorAggregate<int,V>
 * @implements CollectionInterface<V>
 * @implements MergeableInterface<V>
 */
abstract class AbstractCollection implements IteratorAggregate, CollectionInterface, MergeableInterface
{
    use ReduceTrait;

    /**
     * The items managed by this collection
     *
     * @var array<int,V>
     */
    protected array $items = [];

    /**
     * @param V $items
     */
    public function __construct(...$items)
    {
        $this->items = array_values($items);
    }

    /**
     * @param iterable<V> $collections
     */
    public function merge(iterable ...$collections): static
    {
        return $this->buildStatic($this->mergeIterables($collections));
    }

    public function find(callable $callback): mixed
    {
        foreach ($this->items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->getArrayCopy());
    }

    /**
     * @return ArrayIterator<int,V>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * @deprecated
     *
     * @internal
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('immutable');
    }

    /**
     * @deprecated
     *
     * @internal
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('immutable');
    }

    public function getArrayCopy(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function sort(callable $callback): static
    {
        $items = $this->items;
        uasort($items, $callback);

        return $this->buildStatic($items);
    }

    public function map(callable $callback): CollectionInterface
    {
        return new Collection(
            ...array_map(
                $callback,
                $this->items,
                array_keys($this->items),
            ),
        );
    }

    public function filter(callable $callback): static
    {
        return $this->buildStatic(array_filter(
            $this->items,
            $callback,
            ARRAY_FILTER_USE_BOTH
        ));
    }

    public function filterMap(callable $callback): CollectionInterface
    {
        // @phpstan-ignore return.type
        return new Collection(...array_filter(
            array_map(
                $callback,
                $this->items,
                array_keys($this->items),
            ),
            fn ($x) => null !== $x,
        ));
    }

    /**
     * Map and flatten elements of the `Collection` using a callback function
     *
     * Iterates over each value in the `Collection` passing them to the callback
     * function.
     * The callback function must return a iterable result. The result's items
     * will be appended to items of the returned `Collection`
     *
     * @template R
     *
     * @param callable(V): iterable<R> $closure
     *
     * @return static<R>
     */
    public function flatMap(callable $closure): static
    {
        /** @var R[] $flattened */
        $flattened = [];

        foreach ($this->items as $item) {
            $result = $closure($item);
            if (!is_iterable($result)) {
                throw new InvalidArgumentException(
                    'Given callable did not return an iterable'
                );
            }

            $flattened = array_merge($flattened, array_values([...$result]));
        }

        return $this->buildStatic($flattened);
    }

    /**
     * @template R
     *
     * @param callable(V, int): R $callback
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
     * @param iterable<V>[] $arguments
     *
     * @return array<K,V>
     */
    protected function mergeIterables(array $arguments): array
    {
        $preparedArguments = array_map(
            function (iterable $argument): array {
                return TypeUtility::iterableToArray($argument);
            },
            $arguments
        );
        array_unshift($preparedArguments, $this->getArrayCopy());

        return array_merge(...$preparedArguments);
    }

    /**
     * We expect subclasses to keep the constructor's variadic signature. But
     * to allow the subclasses to specify a type, we must not enforce this
     * signature (i.e. we must not add the signature to the `CollectionInterface`)
     *
     * @template T
     *
     * @param array<int|string,T> $items
     *
     * @return static<T>
     */
    private static function buildStatic(array $items): static
    {
        // @phpstan-ignore new.static
        return new static(...$items);
    }
}
