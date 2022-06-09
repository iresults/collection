<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * OOP wrapper for arrays
 */
class Collection extends AbstractCollection
{
    public function __construct(iterable $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Split a string by string
     *
     * @param string $delimiter
     * @param string $input
     * @return static
     */
    public static function fromString(string $delimiter, string $input): CollectionInterface
    {
        if ($input === '') {
            return new static();
        }

        return new static(explode($delimiter, $input));
    }
}
