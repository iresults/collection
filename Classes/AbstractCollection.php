<?php
declare(strict_types=1);

namespace Iresults\Collection;

use ArrayIterator;
use Iresults\Collection\Traits\FilterMapTrait;
use Iresults\Collection\Traits\FilterTrait;
use Iresults\Collection\Traits\MapTrait;
use Iresults\Collection\Traits\PartitionTrait;
use Iresults\Collection\Traits\ReduceTrait;
use Iresults\Collection\Utility\TypeUtility;
use IteratorAggregate;

/**
 * Abstract base collection container
 *
 * @internal
 */
abstract class AbstractCollection implements IteratorAggregate, CollectionInterface, MergeableInterface
{
    use PartitionTrait;
    use ReduceTrait;
    use FilterTrait;
    use MapTrait;
    use FilterMapTrait;

    /**
     * The items managed by this collection
     *
     * @var array
     */
    protected array $items = [];

    /**
     * AbstractCollection constructor
     *
     * This constructor is not allowed to be called directly. So `new SubCollection()` is denied *unless*
     * `SubCollection` explicitly defines a public constructor method (like `BaseTypedCollection` does)
     *
     * @param iterable $items
     */
    protected function __construct(iterable $items = [])
    {
        $this->items = TypeUtility::iterableToArray($items);
    }

    /**
     * @param mixed ...$arguments
     * @return static
     */
    public function merge(...$arguments): CollectionInterface
    {
        return new static($this->mergeArguments($arguments));
    }

    public function find(callable $callback)
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

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    public function getArrayCopy(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callback): CollectionInterface
    {
        $items = $this->items;
        uasort($items, $callback);

        return new static($items);
    }

    public function ksort(callable $callback): CollectionInterface
    {
        $items = $this->items;
        uksort($items, $callback);

        return new static($items);
    }

    protected function mergeArguments(array $arguments): array
    {
        $preparedArguments = array_map(
            function (iterable $argument): array {
                return TypeUtility::iterableToArray($argument);
            },
            $arguments
        );
        array_unshift($preparedArguments, $this->getArrayCopy());

        return call_user_func_array('array_merge', $preparedArguments);
    }
}
