<?php
declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\CollectionInterface;
use Iresults\Collection\Transformer\Map as MapTransformer;

trait MapTrait
{
    public function map(callable $callback): CollectionInterface
    {
        $transformer = new MapTransformer();

        return $transformer->apply($this, $callback, new static());
    }
}
