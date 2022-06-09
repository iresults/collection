<?php
declare(strict_types=1);

namespace Iresults\Collection;

use Iresults\Collection\Transformer\Filter;
use Iresults\Collection\Utility\TypeUtility;

class TypedCollection extends AbstractTypedCollection
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * Do not allow the constructor to be called directly
     */
    protected function __construct()
    {
        parent::__construct([]);
    }

    /**
     * Return a new instance with the given type and data
     *
     * @param string   $type
     * @param iterable $data
     * @return TypedCollection
     */
    public static function withTypeAndData(string $type, iterable $data): TypedCollection
    {
        $instance = new static();
        $instance->type = $type;

        static::assertValidateElementsType($type, $data);
        $instance->items = TypeUtility::iterableToArray($data);

        return $instance;
    }

    /**
     * Return a new empty instance with the given type
     *
     * @param string $type
     * @return TypedCollection
     */
    public static function withType(string $type): TypedCollection
    {
        return static::withTypeAndData($type, []);
    }

    public function mapTyped(callable $callback, string $targetType = null): TypedCollectionInterface
    {
        $target = static::withType($targetType ?? $this->type);

        return (new Transformer\Map())->apply($this, $callback, $target);
    }

    public function filterMapTyped(callable $callback): TypedCollectionInterface
    {
        $target = static::withType($targetType ?? $this->type);

        return (new Transformer\FilterMap())->apply($this, $callback, $target);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function merge(...$arguments): CollectionInterface
    {
        return static::withTypeAndData($this->type, $this->mergeArguments($arguments));
    }

    public function filter(callable $callback): CollectionInterface
    {
        $target = static::withType($this->type);

        return (new Filter())->apply($this, $callback, $target);
    }
}
