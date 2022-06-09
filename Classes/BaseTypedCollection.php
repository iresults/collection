<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * A base class to build custom Typed Collections
 */
abstract class BaseTypedCollection extends AbstractTypedCollection
{
    public function __construct(iterable $items = [])
    {
        static::assertValidateElementsType($this->getType(), $items);
        parent::__construct($items);
    }

    public function mapTyped(callable $callback): TypedCollectionInterface
    {
        $transformer = new Transformer\Map();

        return $transformer->apply($this, $callback, new static());
    }

    public function filterMapTyped(callable $callback): TypedCollectionInterface
    {
        $transformer = new Transformer\FilterMap();

        return $transformer->apply($this, $callback, new static());
    }
}
