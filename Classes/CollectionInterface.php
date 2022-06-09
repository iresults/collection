<?php
declare(strict_types=1);

namespace Iresults\Collection;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * Interface for array functions
 *
 * @template K
 * @template V
 */
interface CollectionInterface extends Countable, ArrayAccess, Traversable
{
    /**
     * Return an array copy of the Collection's data
     *
     * @return array<K, V>
     */
    public function getArrayCopy(): array;

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
     * @return static
     */
    // public function merge(... $arguments): CollectionInterface;

    /**
     * Apply the callback to the elements of the Collection
     *
     * The method returns a new Collection containing all the elements of the Collection after applying the callback function to each one.
     *
     * @template R
     * @param callable(V, K): R $callback Callback to apply
     * @return CollectionInterface<K, R>
     */
    public function map(callable $callback): CollectionInterface;

    /**
     * Reduce the elements of the Collection to a single value using the callback function
     *
     * @template R
     * @param callable(R, V, K): R $callback Callback to apply
     * @param R|null               $carry
     * @return R
     */
    public function reduce(callable $callback, $carry = null);

    /**
     * Filter elements of the Collection using a callback function
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function returns true, the current value is returned into the result Collection. Keys are preserved.
     *
     * @param callable(V, K): bool $callback The callback function to use
     * @return CollectionInterface<K, V>
     */
    public function filter(callable $callback): CollectionInterface;

    /**
     * Map and filter elements of the Collection using a callback function
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function does not return NULL, the current value is returned into the result Collection. If the
     * callback's result is NULL the entry will not be added to the result Collection. Keys are preserved.
     *
     * @template R
     * @param callable(V, K): R|null $callback The callback function to use
     * @return CollectionInterface<K, R>
     */
    public function filterMap(callable $callback): CollectionInterface;

    /**
     * Return the first element of the Collection for which callback returns TRUE
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function returns TRUE, the current value is returned. If no match is found NULL is returned.
     *
     * @param callable(V): bool $callback The callback function to use
     * @return V|null
     */
    public function find(callable $callback);

    /**
     * Join array elements with a string
     *
     * @param string $glue
     * @return string
     */
    public function implode(string $glue = ''): string;

    /**
     * Return a sorted copy of the Collection using the callback function to sort by value
     *
     * Use `ksort()` to sort by key instead
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
     * @return static
     */
    public function sort(callable $callback): CollectionInterface;

    /**
     * Return a sorted copy of the Collection using the callback function to sort by key
     *
     * Use `sort()` to sort by value instead
     *
     * Example for the callback:
     *
     * ```
     *  function($a, $b) {
     *      return $a->character <=> $b->character;
     *  }
     * ```
     *
     * @param callable(K, K): int $callback
     * @return static
     */
    public function ksort(callable $callback): CollectionInterface;

    /**
     * Partition the Collection according to the result of the callback function
     *
     * @template R
     * @param callable(V, K): R $callback
     * @return MapInterface<R, V[]>
     */
    public function partition(callable $callback): MapInterface;
}
