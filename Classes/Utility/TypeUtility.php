<?php

declare(strict_types=1);

namespace Iresults\Collection\Utility;

final class TypeUtility
{
    /**
     * @template K
     * @template V
     *
     * @param iterable<K,V> $input
     *
     * @return array<K,V>
     */
    public static function iterableToArray(iterable $input): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            $output[$key] = $value;
        }

        return $output;
    }
}
