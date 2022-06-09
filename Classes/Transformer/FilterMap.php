<?php
declare(strict_types=1);

namespace Iresults\Collection\Transformer;

use ArrayAccess;

class FilterMap
{
    /**
     * @template K
     * @template V
     * @template R
     * @param iterable<K, V>         $collection
     * @param callable(V, K): R|null $callback
     * @param ArrayAccess<K, R>      $target
     * @return ArrayAccess<K, R>
     */
    public function apply(iterable $collection, callable $callback, ArrayAccess $target): ArrayAccess
    {
        foreach ($collection as $keyObject => $value) {
            $result = $callback($value, $keyObject);
            if (null !== $result) {
                $target->offsetSet($keyObject, $result);
            }
        }

        return $target;
    }
}
