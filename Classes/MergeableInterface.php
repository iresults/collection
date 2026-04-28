<?php

declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Interface for collections that can be merged
 *
 * @template V
 */
interface MergeableInterface
{
    /**
     * Merge one or more `Collection`s into a new `Collection`
     *
     * Merge the elements of one or more `Collection`s together so that the
     * values of one are appended to the end of the previous one.
     *
     * It returns the resulting `Collection`.
     *
     * If the input `Collection`s have the same string keys, then the later
     * value for that key will overwrite the previous one. If, however, the
     * `Collection`s contain numeric keys, the later value will not overwrite
     * the original value, but will be appended.
     *
     * Values in the input `Collection`s with numeric keys will be renumbered
     * with incrementing keys starting from zero in the result `Collection`.
     *
     * @param CollectionInterface<V> $collections
     */
    public function merge(CollectionInterface ...$collections): static;
}
