<?php
declare(strict_types=1);


namespace Iresults\Collection\Tests\Unit\Fixtures;


use Iresults\Collection\BaseTypedCollection;

class PersonCollection extends BaseTypedCollection
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return Person::class;
    }
}
