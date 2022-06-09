<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Object-to-object data store
 *
 * @template K
 * @template V
 */
interface MapInterface extends CollectionInterface
{
    /**
     * Create a new map with the given pairs
     *
     * @param Pair<K, V>[]|array<K, V> $pairs
     * @return MapInterface<K,V>
     */
    public static function withPairs(...$pairs): MapInterface;

    /**
     * Return the array of key objects
     *
     * @return K[]
     */
    public function getKeys(): array;

    /**
     * Return if the given key exists
     *
     * @param string|K $keyObject Key object to lookup or it's hash
     * @return bool
     */
    public function exists($keyObject): bool;

    /**
     * Return the value for the given key
     *
     * @param string|K $keyObject Key object to lookup or it's hash
     * @return V|null
     */
    public function get($keyObject);

    /**
     * Set the value for the given key
     *
     * @param string|K $keyObject Key object to lookup or it's hash
     * @param V               $value
     */
    public function set($keyObject, $value);

    /**
     * Remove the value for the given key from the Map and return it
     *
     * @param string|K $keyObject Key object to lookup or it's hash
     * @return V|null
     */
    public function remove($keyObject);

    /**
     * Return the first key-value pair of the Collection for which callback returns TRUE.
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function returns true, the current value is returned. If no match is found NULL is returned.
     *
     * @param callable $callback The callback function to use
     * @return Pair|null
     */
    public function findPair(callable $callback): ?Pair;
}
