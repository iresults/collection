<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit\Fixtures;

class Person
{
    private $name;

    /**
     * Person constructor.
     *
     * @param $name
     */
    public function __construct($name = null)
    {
        static $counter = 0;
        $this->name = $name ?: ('Person ' . ++$counter);
    }


    public function __toString()
    {
        return $this->name;
    }

    public function getName()
    {
        return $this->name;
    }
}
