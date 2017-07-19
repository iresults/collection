<?php
declare(strict_types=1);

namespace Iresults\Collection;

class TypedCollection extends AbstractTypedCollection
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Do not allow the constructor to be called directly
     */
    protected function __construct()
    {
    }

    /**
     * Returns a new instance with the given type and data
     *
     * @param string             $type
     * @param array|\Traversable $data
     * @return TypedCollection
     */
    public static function collectionWithTypeAndData(string $type, $data): TypedCollection
    {
        $instance = new static();
        $instance->type = $type;
        static::assertValidInput($data);

        static::assertValidateElementsType($type, $data);
        $instance->items = is_array($data) ? $data : iterator_to_array($data);

        return $instance;
    }

    /**
     * Returns a new empty instance with the given type
     *
     * @param string $type
     * @return TypedCollection
     */
    public static function collectionWithType(string $type): TypedCollection
    {
        return static::collectionWithTypeAndData($type, []);
    }

    /**
     * Applies the callback to the elements of the collection
     *
     * The method returns a new Typed Collection containing all the elements of the collection after applying the callback function to each one.
     *
     * @param callable $callback   Callback to apply
     * @param string   $targetType Target type of the new collection. If none is given the current type will be used
     * @return CollectionInterface
     */
    public function mapTyped(callable $callback, string $targetType = null)
    {
        return static::collectionWithTypeAndData(
            $targetType ?? $this->type,
            array_map($callback, $this->getArrayCopy())
        );
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function merge(... $arguments): CollectionInterface
    {
        return static::collectionWithTypeAndData($this->type, $this->mergeArguments($arguments));
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface
    {
        return static::collectionWithTypeAndData(
            $this->type,
            array_filter($this->getArrayCopy(), $callback, $flag)
        );
    }
}
