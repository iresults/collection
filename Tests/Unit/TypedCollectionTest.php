<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use ArrayObject;
use Iresults\Collection\Collection;
use Iresults\Collection\Exception\InvalidArgumentTypeException;
use Iresults\Collection\Tests\Unit\Fixtures\Address;
use Iresults\Collection\Tests\Unit\Fixtures\Person;
use Iresults\Collection\TypedCollection;
use PHPUnit\Framework\TestCase;

class TypedCollectionTest extends TestCase
{
    /**
     * @var TypedCollection
     */
    protected TypedCollection $fixture;

    protected function setUp(): void
    {
        $this->fixture = TypedCollection::withTypeAndData(
            Person::class,
            [new Person('Daniel'), new Person('Gert'), new Person('Loren')]
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
        $collection = TypedCollection::withTypeAndData(Person::class, new ArrayObject($inputArray));

        $this->assertEquals($inputArray, $collection->getArrayCopy());
    }

    /**
     * @test
     */
    public function throwForMixedElementsTest()
    {
        $this->expectException(InvalidArgumentTypeException::class);
        TypedCollection::withTypeAndData(Person::class, [new Person(), new Person(), new Address()]);
    }

    /**
     * @test
     */
    public function mapTest()
    {
        $result = $this->fixture->map(fn(Person $item) => strtoupper($item->getName()));
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['DANIEL', 'GERT', 'LOREN'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function mapTypedTest()
    {
        $result = $this->fixture->mapTyped(fn(Person $item) => new Person(strtoupper($item->getName())));
        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame('Daniel,Gert,Loren', $this->fixture->implode(','));
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(fn(Person $item) => $item->getName() === 'Gert');
        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertEquals([1 => new Person('Gert')], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function offsetSetTest()
    {
        $this->expectException(InvalidArgumentTypeException::class);
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
