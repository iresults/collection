<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * A base class to build custom Typed Collections
 */
abstract class BaseTypedCollection extends AbstractTypedCollection
{
    /**
     * @param array|\Traversable $data
     */
    public function __construct($data)
    {
        static::assertValidInput($data);

        static::assertValidateElementsType($this->getType(), $data);
        $this->items = is_array($data) ? $data : iterator_to_array($data);
    }

    /**
     * Applies the callback to the elements of the collection
     *
     * The method returns a new Typed Collection containing all the elements of the collection after applying the callback function to each one.
     *
     * @param callable $callback Callback to apply
     * @return CollectionInterface
     */
    public function mapTyped(callable $callback)
    {
        return new static(array_map($callback, $this->getArrayCopy()));
    }
}
