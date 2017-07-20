<?php
declare(strict_types=1);

namespace Iresults\Collection;


use Iresults\Collection\Exception\InvalidArgumentTypeException;

abstract class AbstractTypedCollection extends AbstractCollection
{
    /**
     * Returns the managed type
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $newValue)
    {
        $this->validateElementType($this->getType(), $newValue);
        parent::offsetSet($index, $newValue);
    }

    /**
     * @inheritDoc
     */
    public function merge(... $arguments): CollectionInterface
    {
        return new static($this->mergeArguments($arguments));
    }

    /**
     * @inheritdoc
     */
    public function map(callable $callback): CollectionInterface
    {
        return new Collection(array_map($callback, $this->getArrayCopy()));
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface
    {
        return new static(array_filter($this->getArrayCopy(), $callback, $flag));
    }

    /**
     * @param string             $type
     * @param array|\Traversable $elements
     */
    protected static function assertValidateElementsType(string $type, $elements)
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
                static::detectType($element)
            );
            throw new InvalidArgumentTypeException($exceptionMessage);
        }
    }

    /**
     * @param mixed $element
     * @return string
     */
    protected static function detectType($element)
    {
        return is_object($element) ? get_class($element) : gettype($element);
    }


    /**
     * @param $input
     * @throws InvalidArgumentTypeException
     */
    protected static function assertValidInput($input)
    {
        if (!is_array($input) && !($input instanceof \Traversable)) {
            throw new InvalidArgumentTypeException(
                'Input must be either an array or an object'
            );
        }
    }
}
