<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;


use ArrayObject;
use Iresults\Collection\Collection;
use Iresults\Collection\Tests\Unit\Fixtures\Address;
use Iresults\Collection\Tests\Unit\Fixtures\Person;
use Iresults\Collection\TypedCollection;
use PHPUnit\Framework\TestCase;

class TypedCollectionTest extends TestCase
{
    /**
     * @var TypedCollection
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = TypedCollection::withTypeAndData(
            Person::class,
            [new Person('Daniel'), new Person('Gert'), new Person('Loren')]
        );
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function createWithObjectTest()
    {
        $inputArray = [new Person('Daniel'), new Person('Gert'), new Person('Loren')];
        $collection = TypedCollection::withTypeAndData(Person::class, new ArrayObject($inputArray));

        $this->assertEquals($inputArray, $collection->getArrayCopy());
    }

    /**
     * @test
     * @expectedException \Iresults\Collection\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessage Input must be either an array or an object
     */
    public function throwForInvalidInputTest()
    {
        TypedCollection::withTypeAndData(Person::class, 123);
    }

    /**
     * @test
     * @expectedException \Iresults\Collection\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessage Input must be either an array or an object
     */
    public function throwForInvalidClassInputTest()
    {
        TypedCollection::withTypeAndData(Person::class, 'NotAClass');
    }

    /**
     * @test
     * @expectedException \Iresults\Collection\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessage Input must be either an array or an object
     */
    public function throwEmptyInputTest()
    {
        TypedCollection::withTypeAndData(Person::class, null);
    }

    /**
     * @test
     * @expectedException \Iresults\Collection\Exception\InvalidArgumentTypeException
     */
    public function throwForMixedElementsTest()
    {
        TypedCollection::withTypeAndData(Person::class, [new Person(), new Person(), new Address()]);
    }

    /**
     * @test
     */
    public function mapTest()
    {
        $result = $this->fixture->map(
            function (Person $item) {
                return strtoupper($item->getName());
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['DANIEL', 'GERT', 'LOREN'], $result->getArrayCopy());

        $result = $this->fixture->map('strtoupper');
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
            function (Person $item) {
                return new Person(strtoupper($item->getName()));
            }
        );
        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame('Daniel,Gert,Loren', $this->fixture->implode(','));
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(
            function (Person $item) {
                return $item->getName() === 'Gert';
            },
            $flag = 0
        );
        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertEquals([1 => new Person('Gert')], $result->getArrayCopy());
    }

    /**
     * @test
     * @expectedException \Iresults\Collection\Exception\InvalidArgumentTypeException
     */
    public function offsetSetTest()
    {
        $this->fixture['offset'] = 'not a person instance';
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
        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame(9, $result->count());
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
