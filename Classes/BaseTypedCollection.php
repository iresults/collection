<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * A base class to build custom Typed Collections
 */
abstract class BaseTypedCollection extends AbstractTypedCollection
{
    /**
     * @param array|\Traversable $items
     */
    public function __construct($items = [])
    {
        static::assertValidInput($items);

        static::assertValidateElementsType($this->getType(), $items);
        parent::__construct($items);
    }

    /**
     * Applies the callback to the elements of the collection
     *
     * The method returns a new Typed Collection containing all the elements of the collection after applying the callback function to each one.
     *
     * @param callable $callback Callback to apply
     * @return static
     */
    public function mapTyped(callable $callback): TypedCollectionInterface
    {
        return new static(array_map($callback, $this->getArrayCopy()));
    }
}
