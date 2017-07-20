<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Array extension to support array functions
 */
interface MergeableInterface
{
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
    public function merge(...$arguments): CollectionInterface;
}
