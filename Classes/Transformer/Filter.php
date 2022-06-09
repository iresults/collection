<?php
declare(strict_types=1);

namespace Iresults\Collection\Transformer;

use ArrayAccess;

class Filter
{
    /**
     * @template K
     * @template V
     * @param iterable<K,V>        $collection
     * @param callable(V, K): bool $callback
     * @param ArrayAccess<K, V>    $target
     * @return ArrayAccess<K, V>
     */
    public function apply(iterable $collection, callable $callback, ArrayAccess $target): ArrayAccess
    {
        foreach ($collection as $keyObject => $value) {
            if ($callback($value, $keyObject)) {
                $target->offsetSet($keyObject, $value);
            }
        }

        return $target;
    }
}
