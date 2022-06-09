<?php
declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\Collection;
use Iresults\Collection\MapInterface;
use Iresults\Collection\Transformer\Partition;

trait PartitionTrait
{
    public function partition(callable $callback): MapInterface
    {
        $transformer = new Partition();

        return $transformer->apply($this, $callback, Collection::class);
    }
}
