<?php

declare(strict_types=1);

namespace Iresults\Collection;

/**
 * @template K
 * @template V
 *
 * @extends CollectionTransformerInterface<K,V>
 */
interface MapInterface extends CollectionTransformerInterface
{
    /**
     * Return the array of values
     *
     * @return V[]
     */
    public function getValues(): array;

    /**
     * Return the array of keys
     *
     * @return K[]
     */
    public function getKeys(): array;

    /**
     * Return if the given key exists
     *
     * @param string|K $key
     */
    public function exists(mixed $key): bool;

    /**
     * Return the value for the given key
     *
     * @param string|K $key
     *
     * @return V|null
     */
    public function get(mixed $key): mixed;

    /**
     * Return the value for the given key
     *
     * @return V|null
     */
    public function getByHash(string $hash): mixed;

    /**
     * Set the value for the given key
     *
     * @param K $key
     * @param V $value
     */
    public function set(mixed $key, mixed $value): void;

    /**
     * Remove the value for the given key from the `Map` and return it
     *
     * @param string|K $key
     *
     * @return V|null
     */
    public function remove(mixed $key): mixed;

    /**
     * Return the first element of the `Map` for which the callback returns
     * `true`
     *
     * Iterates over each key-value-pair in the `Map` passing them to the
     * callback function.
     * If the callback function returns `true`, the current value is returned.
     * If no match is found `null` is returned.
     *
     * @param callable(V,K): bool $callback
     *
     * @return V|null
     */
    public function find(callable $callback): mixed;

    /**
     * Return the first key-value pair of the `Collection` for which callback
     * returns `true`.
     *
     * Iterates over each value in the `Collection` passing them to the callback
     * function.
     * If the callback function returns `true`, the current value is returned.
     * If no match is found `null` is returned.
     *
     * @param callable(Pair<K,V>):bool $callback The callback function to use
     *
     * @return Pair<K,V>|null
     */
    public function findPair(callable $callback): ?Pair;
}
