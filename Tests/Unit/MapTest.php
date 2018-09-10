<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use Iresults\Collection\CollectionInterface;
use Iresults\Collection\Map;
use Iresults\Collection\Pair;
use PHPUnit\Framework\TestCase;
use stdClass;

class MapTest extends TestCase
{
    /**
     * @var Map
     */
    protected $fixture;

    protected function setUp()
    {
        $objectA = new stdClass();
        $objectA->character = 'a';

        $objectB = new stdClass();
        $objectB->character = 'b';

        $objectC = new stdClass();
        $objectC->character = 'c';

        $this->fixture = new Map(
            [
                [$objectA, 'a'],
                [$objectB, 'b'],
                [$objectC, 'c'],
            ]
        );
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function currentTest()
    {
        $this->assertSame('a', $this->fixture->current());
    }

    /**
     * @test
     */
    public function nextTest()
    {
        $this->assertSame('a', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('b', $this->fixture->current());
        $this->fixture->next();
        $this->assertSame('c', $this->fixture->current());
        $this->fixture->next();
        $this->assertNull($this->fixture->current());
    }


    /**
     * @test
     */
    public function keyTest()
    {
        $this->assertInstanceOf(stdClass::class, $this->fixture->key());
        $this->assertSame('a', $this->fixture->key()->character);
    }


    /**
     * @test
     */
    public function hashKeyTest()
    {
        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'xyz'],
            ]
        );

        $this->assertSame(spl_object_hash($object), $this->fixture->hashKey());
    }

    /**
     * @test
     */
    public function validTest()
    {
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertTrue($this->fixture->valid());
        $this->fixture->next();
        $this->assertFalse($this->fixture->valid());
    }

    /**
     * @test
     */
    public function rewindTest()
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

    /**
     * @test
     */
    public function offsetExistsTest()
    {
        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'xyz'],
            ]
        );


        $this->assertTrue($this->fixture->offsetExists($object));
        $this->assertTrue($this->fixture->exists($object));
        $this->assertTrue(isset($this->fixture[$object]));

        $this->assertTrue($this->fixture->offsetExists(spl_object_hash($object)));
        $this->assertTrue($this->fixture->exists(spl_object_hash($object)));
        $this->assertTrue(isset($this->fixture[spl_object_hash($object)]));

        $object2 = new stdClass();
        $this->assertFalse($this->fixture->offsetExists($object2));
        $this->assertFalse($this->fixture->exists($object2));
        $this->assertFalse(isset($this->fixture[$object2]));

        $this->assertFalse($this->fixture->offsetExists(spl_object_hash($object2)));
        $this->assertFalse($this->fixture->exists(spl_object_hash($object2)));
        $this->assertFalse(isset($this->fixture[spl_object_hash($object2)]));
    }

    /**
     * @test
     */
    public function offsetGetTest()
    {
        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'a'],
            ]
        );


        $this->assertEquals('a', $this->fixture->offsetGet($object));
        $this->assertEquals('a', $this->fixture->get($object));
        $this->assertEquals('a', $this->fixture[$object]);

        $this->assertEquals('a', $this->fixture->offsetGet(spl_object_hash($object)));
        $this->assertEquals('a', $this->fixture->get(spl_object_hash($object)));
        $this->assertEquals('a', $this->fixture[spl_object_hash($object)]);

        $object2 = new stdClass();
        $this->assertNull($this->fixture->offsetGet($object2));
        $this->assertNull($this->fixture->get($object2));
        $this->assertNull($this->fixture[$object2]);

        $this->assertNull($this->fixture->offsetGet(spl_object_hash($object2)));
        $this->assertNull($this->fixture->get(spl_object_hash($object2)));
        $this->assertNull($this->fixture[spl_object_hash($object2)]);
    }

    /**
     * @test
     */
    public function offsetSetTest()
    {
        $object = new stdClass();
        $this->fixture = new Map();

        $this->fixture->offsetSet($object, 'a');
        $this->assertEquals('a', $this->fixture->offsetGet($object));
        $this->assertEquals('a', $this->fixture->offsetGet(spl_object_hash($object)));

        $this->fixture->set($object, 'm');
        $this->assertEquals('m', $this->fixture->offsetGet($object));
        $this->assertEquals('m', $this->fixture->offsetGet(spl_object_hash($object)));

        $this->fixture[$object] = 'v';
        $this->assertEquals('v', $this->fixture->offsetGet($object));
        $this->assertEquals('v', $this->fixture->offsetGet(spl_object_hash($object)));
    }

    /**
     * @test
     */
    public function offsetUnsetTest()
    {
        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'a'],
            ]
        );

        $this->fixture->offsetUnset($object);
        $this->assertNull($this->fixture->offsetGet($object));
        $this->assertNull($this->fixture[$object]);

        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'a'],
            ]
        );
        unset($this->fixture[$object]);
        $this->assertNull($this->fixture->offsetGet($object));
        $this->assertNull($this->fixture[$object]);
    }

    /**
     * @test
     */
    public function findTest()
    {
        $this->fixture = new Map(
            [
                [(object)["character" => 'a'], (object)["number" => 10]],
                [(object)["character" => 'b'], (object)["number" => 20]],
                [(object)["character" => 'c'], (object)["number" => 30]],
                [(object)["character" => 'd'], (object)["number" => 40]],
            ]
        );

        $result = $this->fixture->find(
            function (stdClass $value) {
                return $value->number > 20;
            }
        );

        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertSame($result->number, 30);
    }

    /**
     * @test
     */
    public function findNoneTest()
    {
        $this->fixture = new Map(
            [
                [(object)["character" => 'a'], (object)["number" => 10]],
                [(object)["character" => 'b'], (object)["number" => 20]],
                [(object)["character" => 'c'], (object)["number" => 30]],
                [(object)["character" => 'd'], (object)["number" => 40]],
            ]
        );

        $result = $this->fixture->find(
            function (stdClass $value) {
                return $value->number > 40;
            }
        );

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function findPairTest()
    {
        $this->fixture = new Map(
            [
                [(object)["character" => 'a'], (object)["number" => 10]],
                [(object)["character" => 'b'], (object)["number" => 20]],
                [(object)["character" => 'c'], (object)["number" => 30]],
                [(object)["character" => 'd'], (object)["number" => 40]],
            ]
        );

        $result = $this->fixture->findPair(
            function (Pair $pair) {
                return $pair->getValue()->number > 20;
            }
        );

        $this->assertInstanceOf(Pair::class, $result);
        $this->assertInstanceOf(stdClass::class, $result->getValue());
        $this->assertSame($result->getValue()->number, 30);
        $this->assertInstanceOf(stdClass::class, $result->getKey());
        $this->assertSame($result->getKey()->character, 'c');
    }

    /**
     * @test
     */
    public function findPairNoneTest()
    {
        $this->fixture = new Map(
            [
                [(object)["character" => 'a'], (object)["number" => 10]],
                [(object)["character" => 'b'], (object)["number" => 20]],
                [(object)["character" => 'c'], (object)["number" => 30]],
                [(object)["character" => 'd'], (object)["number" => 40]],
            ]
        );

        $result = $this->fixture->findPair(
            function (Pair $pair) {
                return $pair->getValue()->number > 40;
            }
        );

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function countTest()
    {
        $this->assertEquals(3, $this->fixture->count());

        $object = new stdClass();
        $this->fixture = new Map(
            [
                [$object, 'a'],
            ]
        );
        $this->assertEquals(1, $this->fixture->count());

        $this->fixture->offsetSet(new stdClass(), 123);
        $this->assertEquals(2, $this->fixture->count());

        $this->fixture->offsetUnset($object);
        $this->assertEquals(1, $this->fixture->count());

    }

    /**
     * @test
     */
    public function iteratorTest()
    {
        $iteratorCount = 0;
        foreach ($this->fixture as $keyObject => $value) {
            $iteratorCount++;
            /** @var object $keyObject */
            $this->assertInstanceOf(stdClass::class, $keyObject);
            $this->assertInternalType('string', $value);
            $this->assertEquals($value, $keyObject->character);
        }
        $this->assertSame(3, $iteratorCount);
    }

    /**
     * @test
     */
    public function getArrayCopyTest()
    {
        $this->assertSame(['a', 'b', 'c'], array_values($this->fixture->getArrayCopy()));
    }

    /**
     * @test
     */
    public function getKeysTest()
    {
        $objectA = new stdClass();
        $objectA->character = 'a';

        $objectB = new stdClass();
        $objectB->character = 'b';

        $objectC = new stdClass();
        $objectC->character = 'c';

        $this->fixture = new Map(
            [
                [$objectA, 'a'],
                [$objectB, 'b'],
                [$objectC, 'c'],
            ]
        );

        $this->assertSame([$objectA, $objectB, $objectC], array_values($this->fixture->getKeys()));
    }

    /**
     * @test
     */
    public function mapTest()
    {
        $result = $this->fixture->map(
            function ($keyObject, $value) {
                /** @var object $keyObject */
                assert($value === $keyObject->character);

                return strtoupper($keyObject->character);
            }
        );
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], array_values($result->getArrayCopy()));
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(
            function ($item) {
                return $item->character === 'a';
            },
            $flag = 0
        );
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], array_values($result->getArrayCopy()));
    }

    /**
     * @test
     */
    public function implodeTest()
    {
        $this->assertSame('abc', $this->fixture->implode());
        $this->assertSame('a,b,c', $this->fixture->implode(','));
    }

    /**
     * @test
     */
    public function createWithEmptyArgumentTest()
    {
        $map = new Map([]);
        $this->assertCount(0, $map);
    }

    /**
     * @test
     */
    public function createWithNullValueArgumentTest()
    {
        $object = new stdClass();
        $map = new Map(
            [
                [$object, null],
            ]
        );
        $this->assertCount(1, $map);
        $this->assertSame($object, $map->key());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function createWithInvalidArgumentTest()
    {
        new Map([new stdClass(),]);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function createWithInvalidArgument2Test()
    {
        new Map([[]]);
    }

    /**
     * @test
     */
    public function createWithPairTest()
    {
        $map = Map::withPairs(
            [new stdClass(), 'a'],
            [new stdClass(), 'b'],
            [new stdClass(), 'c']
        );
        $this->assertSame(['a', 'b', 'c'], array_values($map->getArrayCopy()));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function createWithInvalidPairTest()
    {
        $map = Map::withPairs(
            new stdClass(),
            [new stdClass(), 'b'],
            [new stdClass(), 'c']
        );
        $this->assertSame(['a', 'b', 'c'], array_values($map->getArrayCopy()));
    }

    /**
     * @test
     */
    public function sortTest()
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

    /**
     * @test
     */
    public function ksortTest()
    {
        $this->runSortTest(
            function (Map $fixture) {
                return $fixture->ksort(
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

    protected function runSortTest(callable $callback)
    {
        $this->fixture = new Map(
            [
                [(object)["keyObjectProperty" => 'c'], (object)["valueObjectProperty" => 30]],
                [(object)["keyObjectProperty" => 'b'], (object)["valueObjectProperty" => 20]],
                [(object)["keyObjectProperty" => 'a'], (object)["valueObjectProperty" => 10]],
                [(object)["keyObjectProperty" => 'd'], (object)["valueObjectProperty" => 40]],
            ]
        );

        /** @var Map $sorted */
        $sorted = $callback($this->fixture);
        $this->assertInstanceOf(Map::class, $sorted);
        $sortedKeyArray = array_values($sorted->getKeys());
        $sortedArray = array_values($sorted->getArrayCopy());
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
