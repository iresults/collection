<?php

namespace Iresults\Collection\Tests\Unit;

use Iresults\Collection\Pair;
use PHPUnit\Framework\TestCase;

class PairTest extends TestCase
{
    /**
     * @var Pair
     */
    private $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new Pair('a', 'b');
    }

    protected function tearDown()
    {
        unset($this->fixture);
        parent::tearDown();
    }

    public function testGetLeft()
    {
        $this->assertSame('a', $this->fixture->getLeft());
    }

    public function testGetRight()
    {
        $this->assertSame('b', $this->fixture->getRight());
    }

    public function testGetKey()
    {
        $this->assertSame('a', $this->fixture->getKey());
    }

    public function testGetValue()
    {
        $this->assertSame('b', $this->fixture->getValue());
    }

    public function testGetField1()
    {
        $this->assertSame('a', $this->fixture->getField1());
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->fixture->offsetExists(0));
        $this->assertTrue($this->fixture->offsetExists(1));
        $this->assertFalse($this->fixture->offsetExists(2));
        $this->assertFalse($this->fixture->offsetExists(3));
        $this->assertFalse($this->fixture->offsetExists(-1));

        $this->assertTrue(isset($this->fixture[0]));
        $this->assertTrue(isset($this->fixture[1]));
        $this->assertFalse(isset($this->fixture[2]));
        $this->assertFalse(isset($this->fixture[3]));
        $this->assertFalse(isset($this->fixture[-1]));
    }

    public function testOffsetGet()
    {
        $this->assertSame('a', $this->fixture->offsetGet(0));
        $this->assertSame('b', $this->fixture->offsetGet(1));

        $this->assertSame('a', $this->fixture[0]);
        $this->assertSame('b', $this->fixture[1]);
    }

    public function testOffsetSet()
    {
        $this->fixture->offsetSet(0, 'c');
        $this->assertSame('c', $this->fixture->offsetGet(0));
        $this->fixture->offsetSet(1, 'd');
        $this->assertSame('d', $this->fixture->offsetGet(1));

        $this->fixture[0] = 'e';
        $this->assertSame('e', $this->fixture->offsetGet(0));
        $this->fixture[1] = 'f';
        $this->assertSame('f', $this->fixture->offsetGet(1));
    }

    public function testOffsetUnset()
    {
        $this->fixture->offsetUnset(0);
        $this->assertNull($this->fixture->offsetGet(0));
        $this->fixture->offsetUnset(1);
        $this->assertNull($this->fixture->offsetGet(1));

        unset($this->fixture[0]);
        $this->assertNull($this->fixture->offsetGet(0));
        unset($this->fixture[1]);
        $this->assertNull($this->fixture->offsetGet(1));
    }

    /**
     * @expectedException \Iresults\Collection\Exception\OutOfRangeException
     */
    public function testOffsetGetOutOfBound()
    {
        $this->fixture->offsetSet(2, 'c');
    }

    /**
     * @expectedException \Iresults\Collection\Exception\OutOfRangeException
     */
    public function testOffsetSetOutOfBound()
    {
        $this->fixture->offsetSet(2, 'c');
    }

    /**
     * @expectedException \Iresults\Collection\Exception\OutOfRangeException
     */
    public function testOffsetUnsetOutOfBound()
    {
        $this->fixture->offsetSet(2, 'c');
    }
}
