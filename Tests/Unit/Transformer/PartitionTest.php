<?php

declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit\Transformer;

use Iresults\Collection\Collection;
use Iresults\Collection\Map;
use Iresults\Collection\Transformer\Partition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class PartitionTest extends TestCase
{
    private Partition $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new Partition();
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @param iterable<mixed> $testMap
     */
    #[Test]
    #[DataProvider('getIterableTestData')]
    public function testPartitionCollection(iterable $testMap): void
    {
        $result = $this->fixture->apply($testMap, fn ($v) => $v % 2, Collection::class);

        $this->assertSame([1,  0], array_values($result->getKeys()));
        $partition1 = $result->get(1);
        $this->assertInstanceOf(Collection::class, $partition1);
        $this->assertSame([1, 3], $partition1->getArrayCopy());

        $partition2 = $result->get(0);
        $this->assertInstanceOf(Collection::class, $partition2);
        $this->assertSame([2, 4], $partition2->getArrayCopy());
    }

    /**
     * @return array<string|class-string,list<Collection<int>|Map<stdClass,int>|int[]>>
     */
    public static function getIterableTestData(): array
    {
        $o1 = new stdClass();
        $o2 = new stdClass();
        $o3 = new stdClass();
        $o4 = new stdClass();

        return [
            'array' => [
                [
                    1,
                    2,
                    3,
                    4,
                ],
            ],

            Collection::class => [
                new Collection(
                    1,
                    2,
                    3,
                    4,
                ),
            ],

            Map::class => [
                new Map(
                    [$o1, 1],
                    [$o2, 2],
                    [$o3, 3],
                    [$o4, 4],
                ),
            ],
        ];
    }

    public function testPartitionWithBooleanKey(): void
    {
        $result = $this->fixture->apply(
            [
                'a' => -1,
                'b' => 2,
                'c' => -3,
                'd' => 4,
            ],
            fn ($v) => $v > 0,
            Collection::class
        );

        $this->assertSame([false, true], array_values($result->getKeys()));
        $partition1 = $result->get(true);
        $this->assertInstanceOf(Collection::class, $partition1);
        $this->assertSame([2, 4], $partition1->getArrayCopy());

        $partition2 = $result->get(false);
        $this->assertInstanceOf(Collection::class, $partition2);
        $this->assertSame([-1, -3], $partition2->getArrayCopy());
    }
}
