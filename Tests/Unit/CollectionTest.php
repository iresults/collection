<?php

declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit;

use Iresults\Collection\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_values;

final class CollectionTest extends TestCase
{
    #[Test]
    public function fromCollectionTest(): void
    {
        $items = ['a', 'b', 'c'];
        $fixture = new Collection(...new Collection(...$items));
        $this->assertSame($items, $fixture->getArrayCopy());
    }

    #[Test]
    public function reduceTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $result = $fixture->reduce(
            fn (
                ?string $carry,
                string $item,
            ) => ($carry ?? 'the start') . '/' . strtoupper($item)
        );
        $this->assertSame('the start/A/B/C', $result);
    }

    #[Test]
    public function reduceWithInitialValueTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $result = $fixture->reduce(
            function (
                ?string $carry,
                string $item,
            ) {
                assert(null !== $carry);

                return $carry . '/' . strtoupper($item);
            },
            '>'
        );
        $this->assertSame('>/A/B/C', $result);
    }

    #[Test]
    public function mapTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $result = $fixture->map(fn ($item) => strtoupper($item));
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['A', 'B', 'C'], $result->getArrayCopy());
    }

    #[Test]
    public function filterTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $result = $fixture->filter(fn ($item) => 'a' === $item);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], $result->getArrayCopy());
    }

    #[Test]
    public function filterMapTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $result = $fixture->filterMap(
            fn (string $item) => 'b' !== $item ? $item : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['a', 'c'], $result->getArrayCopy());
    }

    #[Test]
    public function filterMapEmptyStringTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');

        // The callback returns an empty string or null => the empty string will be added to the result
        $result = $fixture->filterMap(
            fn (string $value): ?string => 'b' !== $value ? '' : null
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(2, $result->count());
        $this->assertEquals(['', ''], array_values($result->getArrayCopy()));
    }

    #[Test]
    public function findTest(): void
    {
        $fixture = new Collection(10, 20, 30, 40);
        $result = $fixture->find(fn ($item) => $item > 20);
        $this->assertSame(30, $result);
    }

    #[Test]
    public function findNoneTest(): void
    {
        $fixture = new Collection(10, 20, 30, 40);
        $result = $fixture->find(fn ($item) => $item > 40);
        $this->assertNull($result);
    }

    #[Test]
    public function mergeTest(): void
    {
        $fixture = new Collection(10, 20, 30);
        $result = $fixture->merge([1, 2, 3]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(6, $result->count());
        $this->assertSame([10, 20, 30, 1, 2, 3], $result->getArrayCopy());
    }

    #[Test]
    public function multiMergeTest(): void
    {
        $fixture = new Collection(10, 20, 30);
        $result = $fixture->merge([1, 2, 3], [4, 5, 6]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(9, $result->count());
        $this->assertSame([10, 20, 30, 1, 2, 3, 4, 5, 6], $result->getArrayCopy());
    }

    #[Test]
    public function implodeTest(): void
    {
        $fixture = new Collection('a', 'b', 'c');
        $this->assertSame('abc', $fixture->implode());
        $this->assertSame('a,b,c', $fixture->implode(','));
    }

    #[Test]
    public function createFromStringTest(): void
    {
        $result = Collection::fromString(',', 'a,b,c');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(3, $result->count());
        $this->assertSame(['a', 'b', 'c'], $result->getArrayCopy());
    }

    #[Test]
    public function createFromStringSingleElementTest(): void
    {
        $result = Collection::fromString(',', 'a');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(1, $result->count());
        $this->assertSame(['a'], $result->getArrayCopy());
    }

    #[Test]
    public function createFromStringWithEmptyStringTest(): void
    {
        $result = Collection::fromString(',', '');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(0, $result->count());
        $this->assertSame([], $result->getArrayCopy());
    }

    #[Test]
    public function sortTest(): void
    {
        $fixture = new Collection('x', 'g', 'h', 'a');
        $result = $fixture->sort(
            function ($a, $b) {
                return $a <=> $b;
            }
        );
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            ['a', 'g', 'h', 'x'],
            $result->getArrayCopy()
        );
    }

    #[Test]
    public function sortObjectsTest(): void
    {
        $o1 = (object) ['char' => 'x'];
        $o2 = (object) ['char' => 'g'];
        $o3 = (object) ['char' => 'h'];
        $o4 = (object) ['char' => 'a'];

        /** @var Collection<object{char:string}> $fixture */
        $fixture = new Collection($o1, $o2, $o3, $o4);

        $result = $fixture->sort(fn ($a, $b) => $a->char <=> $b->char);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(4, $result->count());
        $this->assertSame(
            [$o4, $o2, $o3, $o1],
            $result->getArrayCopy()
        );
    }
}
