<?php

declare(strict_types=1);

namespace Iresults\Collection;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * Interface for collections
 *
 * @template V
 *
 * @extends Traversable<int,V>
 * @extends ArrayAccess<int,V>
 * @extends CollectionTransformerInterface<int,V>
 */
interface CollectionInterface extends CollectionTransformerInterface, Countable, ArrayAccess, Traversable
{
    /**
     * Return an array copy of the `Collection`'s data
     *
     * @return list<V>
     */
    public function getArrayCopy(): array;

    /**
     * Return the first element of the `Collection` for which the callback
     * returns `true`
     *
     * Iterates over each value in the `Collection` passing them to the callback
     * function.
     * If the callback function returns `true`, the current value is returned.
     * If no match is found `null` is returned.
     *
     * @param callable(V): bool $callback The callback function to use
     *
     * @return V|null
     */
    public function find(callable $callback): mixed;
}
