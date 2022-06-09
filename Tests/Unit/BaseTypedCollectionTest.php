<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use ArrayObject;
use Iresults\Collection\BaseTypedCollection;
use Iresults\Collection\Collection;
use Iresults\Collection\Exception\InvalidArgumentTypeException;
use Iresults\Collection\Map;
use Iresults\Collection\Tests\Unit\Fixtures\Address;
use Iresults\Collection\Tests\Unit\Fixtures\Person;
use Iresults\Collection\Tests\Unit\Fixtures\PersonCollection;
use PHPUnit\Framework\TestCase;
use function array_values;

class BaseTypedCollectionTest extends TestCase
{
    /**
     * @var BaseTypedCollection
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = new PersonCollection(
            [
                new Person('Daniel'),
                new Person('Gert'),
                new Person('Loren'),
            ]
        );
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function createWithObjectTest()
    {
        $inputArray = [new Person('Daniel'), new Person('Gert'), new Person('Loren')];
        $collection = new PersonCollection(new ArrayObject($inputArray));

        $this->assertEquals($inputArray, $collection->getArrayCopy());
    }

    /**
     * @test
     */
    public function throwForMixedElementsTest()
    {
        $this->expectException(InvalidArgumentTypeException::class);
        new PersonCollection([new Person(), new Person(), new Address()]);
    }

    /**
     * @test
     */
    public function mapTest()
    {
        $result = $this->fixture->map(
            fn(Person $item) => strtoupper($item->getName())
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['DANIEL', 'GERT', 'LOREN'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function mapTypedTest()
    {
        $result = $this->fixture->mapTyped(
            fn(Person $item) => new Person(strtoupper($item->getName()))
        );
        $this->assertInstanceOf(BaseTypedCollection::class, $result);
        $this->assertInstanceOf(PersonCollection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame('Daniel,Gert,Loren', $this->fixture->implode(','));
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(
            fn(Person $item) => $item->getName() === 'Gert'
        );
        $this->assertInstanceOf(BaseTypedCollection::class, $result);
        $this->assertInstanceOf(PersonCollection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertEquals([1 => new Person('Gert')], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterMapTest()
    {
        $result = $this->fixture->filterMap(
            fn(Person $item) => $item->getName() !== 'Gert' ? $item : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals([
            0 => new Person('Daniel'),
            2 => new Person('Loren'),
        ], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterMapEmptyStringTest()
    {
        // The callback returns an empty string or null => the empty string will be added to the result
        $result = $this->fixture->filterMap(
            fn(Person $item) => $item->getName() !== 'Gert' ? '' : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['', ''], array_values($result->getArrayCopy()));
    }

    /**
     * @test
     */
    public function multiMergeTest()
    {
        $result = $this->fixture->merge(
            [new Person(), new Person(), new Person()],
            [new Person(), new Person(), new Person()]
        );
        $this->assertInstanceOf(BaseTypedCollection::class, $result);
        $this->assertSame(9, $result->count());
    }

    /**
     * @test
     */
    public function offsetSetTest()
    {
        $person = new Person();
        $this->fixture['offset'] = $person;
        $this->assertCount(4, $this->fixture);
        $this->assertSame($person, $this->fixture['offset']);
    }

    /**
     * @test
     */
    public function offsetSetShouldFailForWrongTypeTest()
    {
        $this->expectException(InvalidArgumentTypeException::class);
        $this->fixture['offset'] = 'not a person instance';
    }

    /**
     * @test
     */
    public function implodeTest()
    {
        $this->assertSame('DanielGertLoren', $this->fixture->implode());
        $this->assertSame('Daniel,Gert,Loren', $this->fixture->implode(','));
    }
}
