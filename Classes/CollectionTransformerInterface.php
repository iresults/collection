<?php

declare(strict_types=1);

namespace Iresults\Collection;

/**
 * @template K
 * @template V
 */
interface CollectionTransformerInterface
{
    /**
     * Apply the callback to the elements of the `Collection`
     *
     * The method returns a new `Collection` containing all the elements of the
     * `Collection` after applying the callback function to each one.
     *
     * @template R
     *
     * @param callable(V, K): R $callback Callback to apply
     *
     * @return CollectionInterface<R>|MapInterface<K,R>
     */
    public function map(callable $callback): CollectionInterface|MapInterface;

    /**
     * Reduce the elements of the `Collection` to a single value using the callback function
     *
     * @template R
     *
     * @param callable(($carry is null ? R|null : R), V, K): R $callback Callback to apply
     * @param R|null                                           $carry
     *
     * @return ($carry is null ? R|null : R)
     */
    public function reduce(callable $callback, mixed $carry = null): mixed;

    /**
     * Filter elements of the `Collection` using a callback function
     *
     * Iterates over each value in the `Collection` passing them to the callback
     * function.
     * If the callback function returns true, the current value is returned into
     * the result `Collection`. Keys are preserved.
     *
     * @param callable(V, K): bool $callback The callback function to use
     */
    public function filter(callable $callback): static;

    /**
     * Map and filter elements of the `Collection` using a callback function
     *
     * Iterates over each value in the `Collection` passing them to the callback
     * function.
     * If the callback function does not return `null`, the current value is
     * returned into the result `Collection`. If the callback's result is `null`
     * the entry will not be added to the result `Collection`.
     * Keys are preserved.
     *
     * @template R
     *
     * @param callable(V, K): (R|null) $callback The callback function to use
     *
     * @return CollectionInterface<R>|MapInterface<K,V>
     */
    public function filterMap(callable $callback): CollectionInterface|MapInterface;

    /**
     * Return a sorted copy of the `Collection` using the callback function to
     * sort by value
     *
     * Example for the callback:
     *
     * ```
     *  function($a, $b) {
     *      return $a->character <=> $b->character;
     *  }
     * ```
     *
     * @param callable(V, V): int $callback
     */
    public function sort(callable $callback): static;

    /**
     * Partition the `Collection` according to the result of the callback function
     *
     * @template R
     *
     * @param callable(V, K): R $callback
     *
     * @return MapInterface<R, CollectionInterface<V>>
     */
    public function partition(callable $callback): MapInterface;
}
