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
     * @param array|\Traversable $items
     */
    protected function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : iterator_to_array($items);
    }

    /**
     * @inheritdoc
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
