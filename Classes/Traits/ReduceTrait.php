<?php
declare(strict_types=1);

namespace Iresults\Collection\Traits;

use Iresults\Collection\Transformer\Reduce;

trait ReduceTrait
{
    public function reduce(callable $callback, $carry = null)
    {
        $transformer = new Reduce();

        return $transformer->apply($this, $callback, $carry);
    }
}
