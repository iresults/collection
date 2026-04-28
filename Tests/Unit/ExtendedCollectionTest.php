<?php

declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use ArrayObject;
use BadMethodCallException;
use Iresults\Collection\Collection;
use Iresults\Collection\Tests\Unit\Fixtures\Address;
use Iresults\Collection\Tests\Unit\Fixtures\Person;
use Iresults\Collection\Tests\Unit\Fixtures\PersonCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TypeError;

use function array_values;

class ExtendedCollectionTest extends TestCase
{
    private PersonCollection $fixture;

    protected function setUp(): void
    {
        $this->fixture = new PersonCollection(
            new Person('Daniel'),
            new Person('Gary'),
            new Person('Loren'),
        );
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
    }

    #[Test]
    public function createWithObjectTest(): void
    {
        $inputArray = [new Person('Daniel'), new Person('Gary'), new Person('Loren')];
        $collection = new PersonCollection(...new ArrayObject($inputArray));

        $this->assertEquals($inputArray, $collection->getArrayCopy());
    }

    #[Test]
    public function throwForMixedElementsTest(): void
    {
        $this->expectException(TypeError::class);
        // @phpstan-ignore argument.type
        new PersonCollection(new Person(''), new Person(''), new Address());
    }

    #[Test]
    public function mapTest(): void
    {
        $result = $this->fixture->map(
            fn (Person $item) => strtoupper($item->name)
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['DANIEL', 'GARY', 'LOREN'], $result->getArrayCopy());
    }

    #[Test]
    public function filterTest(): void
    {
        $result = $this->fixture->filter(
            fn (Person $item) => 'Gary' === $item->name
        );
        $this->assertInstanceOf(PersonCollection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertEquals([new Person('Gary')], $result->getArrayCopy());
    }

    #[Test]
    public function filterMapTest(): void
    {
        $result = $this->fixture->filterMap(
            fn (Person $item) => 'Gary' !== $item->name ? $item : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(
            [new Person('Daniel'), new Person('Loren')],
            $result->getArrayCopy()
        );
    }

    #[Test]
    public function filterMapEmptyStringTest(): void
    {
        // The callback returns an empty string or null => the empty string will be added to the result
        $result = $this->fixture->filterMap(
            fn (Person $item) => 'Gary' !== $item->name ? '' : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['', ''], array_values($result->getArrayCopy()));
    }

    #[Test]
    public function multiMergeTest(): void
    {
        $result = $this->fixture->merge(
            [
                new Person('Organization 2 Person 1'),
                new Person('Organization 2 Person 2'),
                new Person('Organization 2 Person 3'),
            ],
            new PersonCollection(
                new Person('Organization 3 Person 1'),
                new Person('Organization 3 Person 2'),
                new Person('Organization 3 Person 3')
            )
        );
        $this->assertInstanceOf(PersonCollection::class, $result);
        $this->assertSame(9, $result->count());
    }

    #[Test]
    public function offsetSetTest(): void
    {
        $this->expectException(BadMethodCallException::class);
        $person = new Person('');
        $this->fixture->offsetSet(0, $person);
    }

    #[Test]
    public function arrayAccessSetTest(): void
    {
        $this->expectException(BadMethodCallException::class);
        $person = new Person('');
        $this->fixture[0] = $person;
    }

    #[Test]
    public function implodeTest(): void
    {
        $this->assertSame('DanielGaryLoren', $this->fixture->implode());
        $this->assertSame('Daniel,Gary,Loren', $this->fixture->implode(','));
    }
}
