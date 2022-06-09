<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests\Unit\Transformer;

use Iresults\Collection\Collection;
use Iresults\Collection\Map;
use Iresults\Collection\Transformer\Partition;
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
     * @dataProvider getIterableTestData
     * @return void
     */
    public function testPartitionCollection(iterable $testMap)
    {
        $result = $this->fixture->apply($testMap, fn($v) => $v % 2, Collection::class);

        $this->assertSame([1 => 1, 0 => 0], $result->getKeys());
        $partition1 = $result->get(1);
        $this->assertInstanceOf(Collection::class, $partition1);
        $this->assertSame([1, 3], $partition1->getArrayCopy());

        $partition2 = $result->get(0);
        $this->assertInstanceOf(Collection::class, $partition2);
        $this->assertSame([2, 4], $partition2->getArrayCopy());
    }

    public function getIterableTestData(): array
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
                new Collection([
                    1,
                    2,
                    3,
                    4,
                ]),
            ],

            Map::class => [
                new Map([
                    [$o1, 1],
                    [$o2, 2],
                    [$o3, 3],
                    [$o4, 4],
                ]),
            ],
        ];
    }
}
