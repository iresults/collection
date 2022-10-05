<?php
declare(strict_types=1);

namespace Iresults\Collection\Transformer;

use Iresults\Collection\Map;
use Iresults\Collection\MapInterface;

class Partition
{
    /**
     * @template K
     * @template V
     * @template R
     * @param iterable<K, V>    $collection
     * @param callable(V, K): R $callback
     * @param class-string      $targetClass
     * @return MapInterface<R, V[]>
     */
    public function apply(iterable $collection, callable $callback, string $targetClass): MapInterface
    {
        $partitions = new Map();
        foreach ($collection as $key => $value) {
            $newKey = $callback($value, $key);
            $partition = $partitions->get($newKey);
            if ($partition) {
                $partition[] = $value;
            } else {
                $partition = [$value];
            }

            $partitions->set($newKey, $partition);
        }

        /** @var MapInterface $result */
        $result = $partitions->map(fn($partition) => new $targetClass($partition));

        return $result;
    }
}
