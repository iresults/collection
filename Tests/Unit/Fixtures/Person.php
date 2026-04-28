<?php

declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit\Fixtures;

final readonly class Person
{
    public function __construct(public string $name)
    {
        static $counter = 0;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
