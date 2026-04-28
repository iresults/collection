<?php

declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit\Fixtures;

use Iresults\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Person>
 */
class PersonCollection extends AbstractCollection
{
    public function __construct(Person ...$items)
    {
        parent::__construct(...$items);
    }
}
