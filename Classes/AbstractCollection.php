<?php
declare(strict_types=1);

namespace Iresults\Collection;

use IteratorAggregate;

/**
 * Array extension to support array functions
 */
abstract class AbstractCollection implements IteratorAggregate, CollectionInterface
{
    /**
     * The items managed by this collection
     *
     * @var array
     */
    protected $items = [];

    /**
     * Add the given item to the collection
     *
     * @param mixed $item
     * @return CollectionInterface
     */
    public function append($item): CollectionInterface
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Merge one or more Collections into a new Collection
     *
     * Merges the elements of one or more Collections together so that the values of one are appended to the end of the previous one. It returns the resulting Collection.
     *
     * If the input Collections have the same string keys, then the later value for that key will overwrite the previous one. If, however, the Collections contain numeric keys, the later value will not overwrite the original value, but will be appended.
     *
     * Values in the input Collections with numeric keys will be renumbered with incrementing keys starting from zero in the result Collection.
     *
     * @param array $arguments
     * @return CollectionInterface
     */
    public function merge(... $arguments): CollectionInterface
    {
        return new static($this->mergeArguments($arguments));
    }

    /**
     * @inheritdoc
     */
    public function map(callable $callback): CollectionInterface
    {
        return new static(array_map($callback, $this->getArrayCopy()));
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface
    {
        return new static(array_filter($this->getArrayCopy(), $callback, $flag));
    }

    /**
     * @inheritdoc
     */
    public function implode($glue = ''): string
    {
        return implode($glue, $this->getArrayCopy());
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->items);
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
