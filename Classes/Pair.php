<?php

declare(strict_types=1);

namespace Iresults\Collection;

/**
 * @template L
 * @template R
 */
final readonly class Pair
{
    public function __construct(
        public mixed $key,
        public mixed $value,
    ) {
    }
}
