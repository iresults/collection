<?php
declare(strict_types=1);

namespace Iresults\Collection\Transformer;

use ArrayAccess;

class Map
{
    /**
     * @template K
     * @template V
     * @template R
     * @param iterable<K, V>    $collection
     * @param callable(V, K): R $callback
     * @param ArrayAccess<K, R> $target
     * @return ArrayAccess<K, R>
     */
    public function apply(iterable $collection, callable $callback, ArrayAccess $target): ArrayAccess
    {
        foreach ($collection as $keyObject => $value) {
            $target->offsetSet($keyObject, $callback($value, $keyObject));
        }

        return $target;
    }
}
