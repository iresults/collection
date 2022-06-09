<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use Iresults\Collection\Collection;
use Iresults\Collection\Map;
use PHPUnit\Framework\TestCase;
use function array_values;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected Collection $fixture;

    protected function setUp(): void
    {
        $this->fixture = new Collection(['a', 'b', 'c']);
    }

    protected function tearDown(): void
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
        $result = $this->fixture->map(fn($item) => strtoupper($item));
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterTest()
    {
        $result = $this->fixture->filter(fn($item) => $item === 'a');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterMapTest()
    {
        $result = $this->fixture->filterMap(
            fn(string $item) => $item !== 'b' ? $item : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals([
            0 => 'a',
            2 => 'c',
        ], $result->getArrayCopy());
    }

    /**
     * @test
     */
    public function filterMapEmptyStringTest()
    {
        // The callback returns an empty string or null => the empty string will be added to the result
        $result = $this->fixture->filterMap(
            fn(string $value): ?string => $value !== 'b' ? '' : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['', ''], array_values($result->getArrayCopy()));
    }

    /**
     * @test
     */
    public function findTest()
    {
        $this->fixture = new Collection([10, 20, 30, 40]);
        $result = $this->fixture->find(fn($item) => $item > 20);
        $this->assertSame(30, $result);
    }

    /**
     * @test
     */
    public function findNoneTest()
    {
        $this->fixture = new Collection([10, 20, 30, 40]);
        $result = $this->fixture->find(fn($item) => $item > 40);
        $this->assertNull($result);
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

    /**
     * @test
     */
    public function sortTest()
    {
        $this->fixture = new Collection(['x', 'g', 'h', 'a']);

        $result = $this->fixture->sort(
            function ($a, $b) {
                return $a <=> $b;
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            [
                3 => 'a',
                1 => 'g',
                2 => 'h',
                0 => 'x',
            ],
            $result->getArrayCopy()
        );
    }

    /**
     * @test
     */
    public function sortObjectsTest()
    {
        $o1 = (object)['char' => 'x'];
        $o2 = (object)['char' => 'g'];
        $o3 = (object)['char' => 'h'];
        $o4 = (object)['char' => 'a'];
        $this->fixture = new Collection([$o1, $o2, $o3, $o4]);

        $result = $this->fixture->sort(
            function ($a, $b) {
                return $a->char <=> $b->char;
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            [
                3 => $o4,
                1 => $o2,
                2 => $o3,
                0 => $o1,
            ],
            $result->getArrayCopy()
        );
    }

    /**
     * @test
     */
    public function kortTest()
    {
        $o1 = (object)['value' => bin2hex(random_bytes(2))];
        $o2 = (object)['value' => bin2hex(random_bytes(2))];
        $o3 = (object)['value' => bin2hex(random_bytes(2))];
        $o4 = (object)['value' => bin2hex(random_bytes(2))];
        $this->fixture = new Collection(
            [
                'x' => $o1,
                'g' => $o2,
                'h' => $o3,
                'a' => $o4,
            ]
        );

        $result = $this->fixture->ksort(
            function ($a, $b) {
                return $a <=> $b;
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            [
                'a' => $o4,
                'g' => $o2,
                'h' => $o3,
                'x' => $o1,
            ],
            $result->getArrayCopy()
        );
    }

    /**
     * @test
     */
    public function ksortNumericTest()
    {
        $o1 = (object)['value' => bin2hex(random_bytes(2))];
        $o2 = (object)['value' => bin2hex(random_bytes(2))];
        $o3 = (object)['value' => bin2hex(random_bytes(2))];
        $o4 = (object)['value' => bin2hex(random_bytes(2))];
        $this->fixture = new Collection(
            [
                100 => $o1,
                20  => $o2,
                32  => $o3,
                1   => $o4,
            ]
        );

        $result = $this->fixture->ksort(
            function ($a, $b) {
                return $a <=> $b;
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            [
                1   => $o4,
                20  => $o2,
                32  => $o3,
                100 => $o1,
            ],
            $result->getArrayCopy()
        );
    }
}
