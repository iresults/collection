<?php
declare(strict_types=1);

namespace Iresults\Collection\Transformer;

class Reduce
{
    /**
     * @template K
     * @template V
     * @template R
     * @param iterable<K, V>       $collection
     * @param callable(R, V, K): R $callback
     * @param R                    $carry
     * @return R
     */
    public function apply(iterable $collection, callable $callback, $carry)
    {
        foreach ($collection as $keyObject => $value) {
            $carry = $callback($carry, $value, $keyObject);
        }

        return $carry;
    }
}
