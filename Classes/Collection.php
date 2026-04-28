<?php

declare(strict_types=1);

namespace Iresults\Collection;

/**
 * @template V
 *
 * @extends AbstractCollection<V>
 */
final class Collection extends AbstractCollection
{
    /**
     * Split a string by string
     *
     * @param non-empty-string $delimiter
     *
     * @return static<string>
     */
    public static function fromString(
        string $delimiter,
        string $input,
    ): CollectionInterface {
        if ('' === $input) {
            return new static();
        }

        return new static(...explode($delimiter, $input));
    }
}
