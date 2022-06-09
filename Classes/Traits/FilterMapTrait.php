<?php
declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\CollectionInterface;
use Iresults\Collection\Transformer\FilterMap;

trait FilterMapTrait
{
    public function filterMap(callable $callback): CollectionInterface
    {
        return (new FilterMap())->apply($this, $callback, new static());
    }
}
