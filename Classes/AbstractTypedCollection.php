<?php
declare(strict_types=1);

namespace Iresults\Collection;

use Iresults\Collection\Exception\InvalidArgumentTypeException;
use Iresults\Collection\Utility\TypeUtility;

/**
 * @template T
 * @template K
 * @internal
 */
abstract class AbstractTypedCollection extends AbstractCollection implements TypedCollectionInterface
{
    /**
     * Return the managed type
     *
     * @return string|class-string
     */
    abstract public function getType(): string;

    /**
     * @param K $offset
     * @param T $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->validateElementType($this->getType(), $value);
        parent::offsetSet($offset, $value);
    }

    /**
     * @param mixed ...$arguments
     * @return static
     */
    public function merge(...$arguments): CollectionInterface
    {
        return new static($this->mergeArguments($arguments));
    }

    public function map(callable $callback): CollectionInterface
    {
        $transformer = new Transformer\Map();

        // The method returns an un-typed collection
        return $transformer->apply($this, $callback, new Collection());
    }

    public function filterMap(callable $callback): CollectionInterface
    {
        $transformer = new Transformer\FilterMap();

        // The method returns an un-typed collection
        return $transformer->apply($this, $callback, new Collection());
    }

    protected static function assertValidateElementsType(string $type, iterable $elements)
    {
        foreach ($elements as $element) {
            static::validateElementType($type, $element);
        }
    }

    /**
     * @param string $type
     * @param mixed  $element
     * @throws InvalidArgumentTypeException
     */
    protected static function validateElementType(string $type, $element)
    {
        if (!is_a($element, $type)) {
            $exceptionMessage = sprintf(
                'Element is not of expected type %s, %s given',
                $type,
                TypeUtility::detectType($element)
            );
            throw new InvalidArgumentTypeException($exceptionMessage);
        }
    }
}
