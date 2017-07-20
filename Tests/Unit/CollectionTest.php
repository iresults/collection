<?php
declare(strict_types=1);


/**
 * @author COD
 * Created 15.09.15 12:21
 */


namespace Iresults\Collection\Tests\Unit;


use Iresults\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Collection(['a', 'b', 'c']);
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function fromCollectionTest()
    {
        $items = ['a', 'b', 'c'];
        $this->fixture = new Collection(new Collection($items));
        $this->assertSame($items, $this->fixture->getArrayCopy());
    }

    /**
     * @test
     */
    public function mapTest()
    {
        $result = $this->fixture->map(
            function ($item) {
                return strtoupper($item);
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], $result->getArrayCopy());

        $result = $this->fixture->map('strtoupper');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(
            function ($item) {
                return $item === 'a';
            },
            $flag = 0
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function mergeTest()
    {
        $result = $this->fixture->merge([1, 2, 3]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(6, $result->count());
        $this->assertSame(['a', 'b', 'c', 1, 2, 3], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function multiMergeTest()
    {
        $result = $this->fixture->merge([1, 2, 3], [4, 5, 6]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(9, $result->count());
        $this->assertSame(['a', 'b', 'c', 1, 2, 3, 4, 5, 6], $result->getArrayCopy());
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
    public function createFromStringTest()
    {
        $result = Collection::fromString(',', 'a,b,c');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['a', 'b', 'c'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function createFromStringSingleElementTest()
    {
        $result = Collection::fromString(',', 'a');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function createFromStringWithEmptyStringTest()
    {
        $result = Collection::fromString(',', '');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(0, $result->count());
        $this->assertSame([], $result->getArrayCopy());
    }
}
