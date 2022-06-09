<?php
declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\CollectionInterface;
use Iresults\Collection\Transformer\Filter;

trait FilterTrait
{
    public function filter(callable $callback): CollectionInterface
    {
        return (new Filter())->apply($this, $callback, new static());
    }
}
