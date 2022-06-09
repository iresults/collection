<?php
declare(strict_types=1);

namespace Iresults\Collection\Utility;

use function get_class;
use function gettype;
use function is_object;

class TypeUtility
{
    public static function iterableToArray(iterable $input): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            $output[$key] = $value;
        }

        return $output;
    }

    public static function detectType($element): string
    {
        return is_object($element) ? get_class($element) : gettype($element);
    }
}
