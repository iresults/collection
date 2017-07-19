<?php
declare(strict_types=1);

namespace Iresults\Collection;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * Interface for array functions
 */
interface CollectionInterface extends Countable, ArrayAccess, Traversable
{
    /**
     * Returns an array copy of the Collection's data
     *
     * @return array
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
     * @return CollectionInterface
     */
    // public function merge(... $arguments): CollectionInterface;

    /**
     * Applies the callback to the elements of the Collection
     *
     * The method returns a new Collection containing all the elements of the Collection after applying the callback function to each one.
     *
     * @param callable $callback Callback to apply
     * @return CollectionInterface
     */
    public function map(callable $callback): CollectionInterface;

    /**
     * Filters elements of the Collection using a callback function
     *
     * Iterates over each value in the Collection passing them to the callback function.
     * If the callback function returns true, the current value is returned into the result Collection. Keys are preserved.
     *
     * @param callable $callback The callback function to use
     * @param int      $flag     Flag determining what arguments are sent to callback: ARRAY_FILTER_USE_KEY / ARRAY_FILTER_USE_BOTH
     * @return CollectionInterface
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface;

    /**
     * Join array elements with a string
     *
     * @param string $glue
     * @return string
     */
    public function implode($glue = ''): string;
}
