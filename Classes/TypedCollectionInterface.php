<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Interface for Typed Collections
 */
interface TypedCollectionInterface
{
    /**
     * Applies the callback to the elements of the collection
     *
     * The method returns a new Typed Collection containing all the elements of the collection after applying the callback function to each one.
     *
     * @param callable $callback Callback to apply
     * @return static
     */
    public function mapTyped(callable $callback): TypedCollectionInterface;
}
