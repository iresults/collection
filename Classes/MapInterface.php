<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Object-to-object data store
 */
interface MapInterface extends CollectionInterface
{
    /**
     * Create a new map with the given pairs
     *
     * @param array $pairs
     * @return MapInterface
     */
    public static function createWithPairs(...$pairs): MapInterface;

    /**
     * Returns the array of key objects
     *
     * @return object[]
     */
    public function getKeys(): array;

    /**
     * Returns if the given key exists
     *
     * @param object|string $keyObject Key object to lookup or it's hash
     * @return bool
     */
    public function exists($keyObject): bool;

    /**
     * Returns the value for the given key
     *
     * @param object|string $keyObject Key object to lookup or it's hash
     * @return mixed
     */
    public function get($keyObject);

    /**
     * Sets the value for the given key
     *
     * @param object|string $keyObject Key object to lookup or it's hash
     * @param mixed         $value
     */
    public function set($keyObject, $value);
}
