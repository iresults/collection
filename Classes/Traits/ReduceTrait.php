<?php

declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\Transformer\Reduce;

trait ReduceTrait
{
    public function reduce(callable $callback, mixed $carry = null): mixed
    {
        $transformer = new Reduce();

        return $transformer->apply($this, $callback, $carry);
    }
}
