<?php

declare(strict_types=1);

namespace Iresults\Collection\Transformer;

use Iresults\Collection\CollectionInterface;
use Iresults\Collection\Map;

final class Partition
{
    /**
     * @template K
     * @template V
     * @template R of string|int|float|bool|object
     *
     * @param iterable<K, V>                       $collection
     * @param callable(V, K): R                    $callback
     * @param class-string<CollectionInterface<V>> $collectionClass
     *
     * @return Map<R, CollectionInterface<V>>
     */
    public function apply(
        iterable $collection,
        callable $callback,
        string $collectionClass,
    ): Map {
        /** @var Map<R,V[]> $partitions */
        $partitions = new Map();
        foreach ($collection as $key => $value) {
            /** @var R $partitionKey */
            $partitionKey = $callback($value, $key);
            $partition = $partitions->get($partitionKey);
            if ($partition) {
                $partition[] = $value;
            } else {
                $partition = [$value];
            }

            $partitions->set($partitionKey, $partition);
        }

        /** @var Map<R, CollectionInterface<V>> $result */
        $result = $partitions->map(
            fn (array $partition): CollectionInterface => new $collectionClass(...$partition)
        );

        return $result;
    }
}
