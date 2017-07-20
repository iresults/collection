<?php
declare(strict_types=1);

namespace Iresults\Collection;

/**
 * Array extension to support array functions
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * Split a string by string
     *
     * @param string $delimiter
     * @param string $input
     * @return CollectionInterface
     */
    public static function fromString(string $delimiter, string $input): CollectionInterface
    {
        if ($input === '') {
            return new static();
        }

        return new static(explode($delimiter, $input));
    }
}
