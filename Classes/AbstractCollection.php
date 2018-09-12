<?php
declare(strict_types=1);

namespace Iresults\Collection;

use IteratorAggregate;

/**
 * Abstract base collection container
 */
abstract class AbstractCollection implements IteratorAggregate, CollectionInterface, MergeableInterface
{
    /**
     * The items managed by this collection
     *
     * @var array
     */
    protected $items = [];

    /**
     * AbstractCollection constructor
     *
     * This constructor is not allowed to be called directly. So `new SubCollection()` is denied *unless*
     * `SubCollection` explicitly defines a public constructor method (like `BaseTypedCollection` does)
     *
     * @param iterable|array|\Traversable $items
     */
    protected function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : iterator_to_array($items);
    }

    /**
     * @param mixed ...$arguments
     * @return static
     */
    public function merge(... $arguments): CollectionInterface
    {
        return new static($this->mergeArguments($arguments));
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback): CollectionInterface
    {
        return new static(array_map($callback, $this->getArrayCopy()));
    }

    /**
     * @param callable $callback
     * @param int      $flag
     * @return static
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface
    {
        return new static(array_filter($this->getArrayCopy(), $callback, $flag));
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

    public function implode($glue = ''): string
    {
        return implode($glue, $this->getArrayCopy());
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function getArrayCopy(): array
    {
        return $this->items;
    }

    public function count()
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

    /**
     * @param array $arguments
     * @return array
     */
    protected function mergeArguments(array $arguments): array
    {
        $preparedArguments = array_map(
            function ($argument) {
                return is_array($argument) ? $argument : iterator_to_array($argument);
            },
            $arguments
        );
        array_unshift($preparedArguments, $this->getArrayCopy());

        return call_user_func_array('array_merge', $preparedArguments);
    }
}
