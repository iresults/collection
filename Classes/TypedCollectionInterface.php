<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Interface for Typed Collections
 *
 * @template K
 * @template V
 */
interface TypedCollectionInterface
{
    /**
     * Apply the callback to the elements of the collection
     *
     * The method returns a new Typed Collection containing all the elements of the collection after applying the callback function to each one.
     *
     * @param callable(V, K): V $callback Callback to apply
     * @return TypedCollectionInterface<K, V>
     */
    public function mapTyped(callable $callback): TypedCollectionInterface;

    /**
     * Map and filter elements of the Collection using a callback function
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function does not return NULL, the current value is returned into the result Collection. If the
     * callback's result is NULL the entry will not be added to the result Collection. Keys are preserved.
     * The method returns a new Typed Collection.
     *
     * @template R
     * @param callable(V, K): R|null $callback The callback function to use
     * @return TypedCollectionInterface<K, R>
     */
    public function filterMapTyped(callable $callback): TypedCollectionInterface;
}
