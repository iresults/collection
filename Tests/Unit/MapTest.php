<?php

/** @noinspection PhpIllegalArrayKeyTypeInspection */
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use InvalidArgumentException;
use Iresults\Collection\Collection;
use Iresults\Collection\Map;
use Iresults\Collection\Pair;
use Iresults\Collection\Tests\Unit\Fixtures\Person;
use Iresults\Collection\Tests\Unit\Fixtures\PersonalInformation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_values;

final class MapTest extends TestCase
{
    /**
     * @var Map<stdClass,string>
     */
    private Map $fixture;

    protected function setUp(): void
    {
        $objectA = new stdClass();
        $objectA->character = 'a';

        $objectB = new stdClass();
        $objectB->character = 'b';

        $objectC = new stdClass();
        $objectC->character = 'c';

        $this->fixture = new Map(
            [$objectA, 'a'],
            [$objectB, 'b'],
            [$objectC, 'c'],
        );
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
    }

    #[Test]
    public function currentTest(): void
    {
        $this->assertSame('a', $this->fixture->current());
    }

    #[Test]
    public function nextTest(): void
    {
        $this->assertSame('a', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('b', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('c', $this->fixture->current());
        $this->fixture->next();
        $this->assertNull($this->fixture->current());
    }

    #[Test]
    public function keyTest(): void
    {
        $this->assertInstanceOf(stdClass::class, $this->fixture->key());
        $this->assertSame('a', $this->fixture->key()->character);
    }

    #[Test]
    public function validTest(): void
    {
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertFalse($this->fixture->valid());
    }

    #[Test]
    public function rewindTest(): void
    {
        $this->assertSame('a', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('b', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('c', $this->fixture->current());
        $this->fixture->next();
        $this->assertNull($this->fixture->current());

        $this->fixture->rewind();
        $this->assertSame('a', $this->fixture->current());
    }

    #[Test]
    public function offsetExistsTest(): void
    {
        $object = new stdClass();
        $fixture = new Map(
            [$object, 'xyz'],
        );

        $this->assertTrue($fixture->offsetExists($object));
        $this->assertTrue($fixture->exists($object));
        $this->assertTrue(isset($fixture[$object]));

        $this->assertTrue($fixture->exists(spl_object_hash($object)));

        $object2 = new stdClass();
        $this->assertFalse($fixture->offsetExists($object2));
        $this->assertFalse($fixture->exists($object2));
        $this->assertFalse(isset($fixture[$object2]));

        $this->assertFalse($fixture->exists(spl_object_hash($object2)));
    }

    #[Test]
    public function offsetGetTest(): void
    {
        $object = new stdClass();
        $fixture = new Map(
            [$object, 'a'],
        );

        $this->assertEquals('a', $fixture->offsetGet($object));
        $this->assertEquals('a', $fixture->get($object));
        $this->assertEquals('a', $fixture[$object]);

        $this->assertEquals('a', $fixture->get(spl_object_hash($object)));

        $object2 = new stdClass();
        $this->assertNull($fixture->offsetGet($object2));
        $this->assertNull($fixture->get($object2));

        $this->assertNull($fixture->get(spl_object_hash($object2)));
    }

    #[Test]
    public function offsetGetBooleanKeysTest(): void
    {
        $fixture = new Map(
            [false, 'a'],
            [true, 'b'],
        );

        $this->assertEquals('a', $fixture->offsetGet(false));
        $this->assertEquals('a', $fixture->get(false));
        $this->assertEquals('a', $fixture[false]);

        $this->assertEquals('b', $fixture->offsetGet(true));
        $this->assertEquals('b', $fixture->get(true));
        $this->assertEquals('b', $fixture[true]);
    }

    #[Test]
    public function offsetSetTest(): void
    {
        $object = new stdClass();
        $fixture = new Map();

        $fixture->offsetSet($object, 'a');
        $this->assertEquals('a', $fixture->offsetGet($object));
        $this->assertEquals('a', $fixture->offsetGet(spl_object_hash($object)));

        $fixture->set($object, 'm');
        $this->assertEquals('m', $fixture->offsetGet($object));
        $this->assertEquals('m', $fixture->offsetGet(spl_object_hash($object)));

        $fixture[$object] = 'v';
        $this->assertEquals('v', $fixture->offsetGet($object));
        $this->assertEquals('v', $fixture->offsetGet(spl_object_hash($object)));
    }

    #[Test]
    public function offsetSetBooleanKeysTest(): void
    {
        $fixture = new Map();

        $fixture->offsetSet(true, 'a');
        $this->assertEquals('a', $fixture->offsetGet(true));

        $fixture->set(false, 'm');
        $this->assertEquals('m', $fixture->offsetGet(false));

        $fixture[true] = 'v';
        $this->assertEquals('v', $fixture->offsetGet(true));
    }

    #[Test]
    public function offsetUnsetTest(): void
    {
        $object = new stdClass();
        $fixture = new Map(
            [$object, 'a'],
        );

        $fixture->offsetUnset($object);
        $this->assertNull($fixture->offsetGet($object));
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertNull($fixture[$object]);

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }

        $object = new stdClass();
        $fixture = new Map(
            [$object, 'a'],
        );
        unset($fixture[$object]);
        $this->assertNull($fixture->offsetGet($object));
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertNull($fixture[$object]);

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }

        $object = new stdClass();
        $fixture = new Map(
            [$object, 'a'],
        );
        $removedValue = $fixture->remove($object);
        $this->assertSame('a', $removedValue);
        $this->assertNull($fixture->offsetGet($object));
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertNull($fixture[$object]);

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }
    }

    #[Test]
    public function offsetUnsetEmptyValueTest(): void
    {
        $emptyValue = [];
        $object = new stdClass();
        $fixture = new Map(
            [$object, $emptyValue],
        );

        $fixture->offsetUnset($object);
        $this->assertNull($fixture->offsetGet($object));

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }

        $object = new stdClass();
        $fixture = new Map(
            [$object, $emptyValue],
        );
        unset($fixture[$object]);
        $this->assertNull($fixture->offsetGet($object));

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }

        $object = new stdClass();
        $fixture = new Map(
            [$object, $emptyValue],
        );
        $removedValue = $fixture->remove($object);
        $this->assertSame($emptyValue, $removedValue);
        $this->assertNull($fixture->offsetGet($object));

        foreach ($fixture as $_) {
            $this->fail('Map must be empty');
        }
    }

    #[Test]
    public function findTest(): void
    {
        $fixture = new Map(
            [(object) ['character' => 'a'], (object) ['number' => 10]],
            [(object) ['character' => 'b'], (object) ['number' => 20]],
            [(object) ['character' => 'c'], (object) ['number' => 30]],
            [(object) ['character' => 'd'], (object) ['number' => 40]],
        );

        $result = $fixture->find(
            function (stdClass $value) {
                return $value->number > 20;
            }
        );

        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertSame($result->number, 30);
    }

    #[Test]
    public function findNoneTest(): void
    {
        $fixture = new Map(
            [(object) ['character' => 'a'], (object) ['number' => 10]],
            [(object) ['character' => 'b'], (object) ['number' => 20]],
            [(object) ['character' => 'c'], (object) ['number' => 30]],
            [(object) ['character' => 'd'], (object) ['number' => 40]],
        );

        $result = $fixture->find(
            function (stdClass $value) {
                return $value->number > 40;
            }
        );

        $this->assertNull($result);
    }

    #[Test]
    public function findPairTest(): void
    {
        $fixture = new Map(
            [(object) ['character' => 'a'], (object) ['number' => 10]],
            [(object) ['character' => 'b'], (object) ['number' => 20]],
            [(object) ['character' => 'c'], (object) ['number' => 30]],
            [(object) ['character' => 'd'], (object) ['number' => 40]],
        );

        $result = $fixture->findPair(
            fn (Pair $pair) => $pair->value->number > 20
        );

        $this->assertInstanceOf(Pair::class, $result);
        $this->assertInstanceOf(stdClass::class, $result->value);
        $this->assertSame($result->value->number, 30);
        $this->assertInstanceOf(stdClass::class, $result->key);
        $this->assertSame($result->key->character, 'c');
    }

    #[Test]
    public function findPairNoneTest(): void
    {
        $fixture = new Map(
            [(object) ['character' => 'a'], (object) ['number' => 10]],
            [(object) ['character' => 'b'], (object) ['number' => 20]],
            [(object) ['character' => 'c'], (object) ['number' => 30]],
            [(object) ['character' => 'd'], (object) ['number' => 40]],
        );

        $result = $fixture->findPair(
            fn (Pair $pair) => $pair->value->number > 40
        );

        $this->assertNull($result);
    }

    #[Test]
    public function countTest(): void
    {
        $this->assertEquals(3, $this->fixture->count());

        $object = new stdClass();
        $fixture = new Map(
            [$object, 'a'],
        );
        $this->assertEquals(1, $fixture->count());

        $fixture->offsetSet(new stdClass(), '123');
        $this->assertEquals(2, $fixture->count());

        $fixture->offsetUnset($object);
        $this->assertEquals(1, $fixture->count());
    }

    #[Test]
    public function iteratorTest(): void
    {
        $iteratorCount = 0;
        foreach ($this->fixture as $keyObject => $value) {
            ++$iteratorCount;
            /* @var object $keyObject */
            $this->assertInstanceOf(stdClass::class, $keyObject);
            $this->assertIsString($value);
            $this->assertEquals($value, $keyObject->character);
        }
        $this->assertSame(3, $iteratorCount);
    }

    #[Test]
    public function getValuesTest(): void
    {
        $this->assertSame(['a', 'b', 'c'], array_values($this->fixture->getValues()));
    }

    #[Test]
    public function getKeysTest(): void
    {
        $objectA = new stdClass();
        $objectA->character = 'a';

        $objectB = new stdClass();
        $objectB->character = 'b';

        $objectC = new stdClass();
        $objectC->character = 'c';

        $fixture = new Map(
            [$objectA, 'a'],
            [$objectB, 'b'],
            [$objectC, 'c'],
        );

        $this->assertSame([$objectA, $objectB, $objectC], array_values($fixture->getKeys()));
    }

    #[Test]
    public function reduceTest(): void
    {
        $result = $this->fixture->reduce(
            function ($carry, string $value, stdClass $keyObject) {
                /* @var object $keyObject */
                assert($value === $keyObject->character);

                return ($carry ?? 'the-start') . '/' . $value . strtoupper($keyObject->character);
            }
        );
        $this->assertSame('the-start/aA/bB/cC', $result);
    }

    #[Test]
    public function reduceWithInitialCarryTest(): void
    {
        $result = $this->fixture->reduce(
            function ($carry, string $value, stdClass $keyObject) {
                /* @var object $keyObject */
                assert($value === $keyObject->character);
                assert(null !== $carry);

                return $carry . '/' . $value . strtoupper($keyObject->character);
            },
            '>'
        );
        $this->assertSame('>/aA/bB/cC', $result);
    }

    #[Test]
    public function mapTest(): void
    {
        $result = $this->fixture->map(
            function (string $value, stdClass $keyObject) {
                /* @var object $keyObject */
                assert($value === $keyObject->character);

                return strtoupper($keyObject->character);
            }
        );
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], array_values($result->getValues()));
    }

    #[Test]
    public function mapPersonTest(): void
    {
        /** @var Map<Person,PersonalInformation> $fixture */
        $fixture = new Map(
            new Pair(new Person('Daniel'), new PersonalInformation(37)),
            new Pair(new Person('Gary'), new PersonalInformation(61)),
            new Pair(new Person('Loren'), new PersonalInformation(23)),
        );
        $result = $fixture->map(
            fn (
                PersonalInformation $pi,
                Person $person,
            ) => $person->name . ' is ' . $pi->age . ' years old'
        );
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(
            [
                'Daniel is 37 years old',
                'Gary is 61 years old',
                'Loren is 23 years old',
            ],
            array_values($result->getValues())
        );
    }

    #[Test]
    public function filterTest(): void
    {
        $result = $this->fixture->filter(fn (string $value) => 'a' === $value);
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], array_values($result->getValues()));
    }

    #[Test]
    public function filterByKeyTest(): void
    {
        $result = $this->fixture->filter(fn ($_value, stdClass $item) => 'a' === $item->character);
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], array_values($result->getValues()));
    }

    #[Test]
    public function filterMapTest(): void
    {
        $result = $this->fixture->filterMap(
            fn (string $value) => 'b' !== $value ? $value : null
        );
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['a', 'c'], array_values($result->getValues()));
    }

    #[Test]
    public function filterMapEmptyStringTest(): void
    {
        // The callback returns an empty string or null => the empty string will be added to the result
        $result = $this->fixture->filterMap(
            fn (string $value): ?string => 'b' !== $value ? '' : null
        );
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['', ''], array_values($result->getValues()));
    }

    #[Test]
    public function createWithEmptyArgumentTest(): void
    {
        $map = new Map();
        $this->assertCount(0, $map);
    }

    #[Test]
    public function createWithNullValueArgumentTest(): void
    {
        $object = new stdClass();
        $map = new Map(
            [$object, null],
        );
        $this->assertCount(1, $map);
        $this->assertSame($object, $map->key());
    }

    #[Test]
    #[DataProvider('createWithInvalidArgumentDataProvider')]
    public function createWithInvalidArgumentTest(mixed $input): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Map($input);
    }

    /**
     * @return array<non-empty-string, non-empty-list<mixed>>
     */
    public static function createWithInvalidArgumentDataProvider(): array
    {
        return [
            'empty pair'                      => [[]],
            'incomplete pair (missing key 1)' => [[0 => 'hello']],
            'incomplete pair (missing key 0)' => [[1 => 'world']],
        ];
    }

    #[Test]
    public function createWithPairTest(): void
    {
        $map = new Map(
            new Pair(new stdClass(), 'a'),
            new Pair(new stdClass(), 'b'),
            new Pair(new stdClass(), 'c'),
        );
        $this->assertSame(['a', 'b', 'c'], array_values($map->getValues()));
    }

    #[Test]
    public function partitionTest(): void
    {
        $objectA = new stdClass();
        $objectA->character = 'a';

        $objectB = new stdClass();
        $objectB->character = 'b';

        $objectC = new stdClass();
        $objectC->character = 'c';

        /** @var Map<object,float> $fixture */
        $fixture = new Map(
            new Pair($objectA, 10.0),
            new Pair($objectB, 13.0),
            new Pair($objectC, 12.0),
        );
        $partitions = $fixture->partition(
            fn (float $v, object $k) => $v < 13 ? 'child' : 'teen'
        );
        $this->assertInstanceOf(Collection::class, $partitions->get('child'));
        $this->assertInstanceOf(Collection::class, $partitions->get('teen'));
        $this->assertNull($partitions->get('elderly'));
    }

    #[Test]
    public function sortTest(): void
    {
        $this->runSortTest(
            function (Map $fixture) {
                return $fixture->sort(
                    function ($a, $b) {
                        // Make sure the value-object is given and *NOT* the key-object
                        $this->assertFalse(property_exists($a, 'keyObjectProperty'));
                        $this->assertFalse(property_exists($b, 'keyObjectProperty'));

                        return $a->valueObjectProperty <=> $b->valueObjectProperty;
                    }
                );
            }
        );
    }

    #[Test]
    public function sortByKeyTest(): void
    {
        $this->runSortTest(
            function (Map $fixture) {
                return $fixture->sortByKey(
                    function ($a, $b) {
                        // Make sure the key-object is given and *NOT* the value-object
                        $this->assertFalse(property_exists($a, 'valueObjectProperty'));
                        $this->assertFalse(property_exists($b, 'valueObjectProperty'));

                        return $a->keyObjectProperty <=> $b->keyObjectProperty;
                    }
                );
            }
        );
    }

    protected function runSortTest(callable $callback): void
    {
        /** @var Map<object{keyObjectProperty:string},object{valueObjectProperty:int}> $fixture */
        $fixture = new Map(
            [(object) ['keyObjectProperty' => 'c'], (object) ['valueObjectProperty' => 30]],
            [(object) ['keyObjectProperty' => 'b'], (object) ['valueObjectProperty' => 20]],
            [(object) ['keyObjectProperty' => 'a'], (object) ['valueObjectProperty' => 10]],
            [(object) ['keyObjectProperty' => 'd'], (object) ['valueObjectProperty' => 40]],
        );

        /** @var Map<object{keyObjectProperty:string},object{valueObjectProperty:int}> $sorted */
        $sorted = $callback($fixture);
        $this->assertInstanceOf(Map::class, $sorted);
        $sortedKeyArray = array_values($sorted->getKeys());
        $sortedArray = array_values($sorted->getValues());
        $this->assertSame('a', $sortedKeyArray[0]->keyObjectProperty);
        $this->assertSame(10, $sortedArray[0]->valueObjectProperty);
        $this->assertSame('b', $sortedKeyArray[1]->keyObjectProperty);
        $this->assertSame(20, $sortedArray[1]->valueObjectProperty);
        $this->assertSame('c', $sortedKeyArray[2]->keyObjectProperty);
        $this->assertSame(30, $sortedArray[2]->valueObjectProperty);
        $this->assertSame('d', $sortedKeyArray[3]->keyObjectProperty);
        $this->assertSame(40, $sortedArray[3]->valueObjectProperty);
    }
}
