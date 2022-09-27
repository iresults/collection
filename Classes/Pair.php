<?php
declare(strict_types=1);

namespace Iresults\Collection;

use Iresults\Collection\Exception\InvalidArgumentTypeException;
use Iresults\Collection\Exception\OutOfRangeException;

/**
 * @template L
 * @template R
 */
class Pair implements \ArrayAccess
{
    /**
     * @var L
     */
    private $left;

    /**
     * @var R
     */
    private $right;

    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Return the left value
     *
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @see getLeft()
     */
    public function getField1()
    {
        return $this->getLeft();
    }

    /**
     * @see getLeft()
     */
    public function getKey()
    {
        return $this->getLeft();
    }

    /**
     * Return the `right` value
     *
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @see getRight()
     */
    public function getField2()
    {
        return $this->getRight();
    }

    /**
     * @see getRight()
     */
    public function getValue()
    {
        return $this->getRight();
    }

    public function offsetExists($offset): bool
    {
        return is_int($offset) && $offset >= 0 && $offset < 2;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if ($offset === 0) {
            return $this->left;
        } elseif ($offset === 1) {
            return $this->right;
        }
        throw $this->throwOffsetException($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if ($offset === 0) {
            $this->left = $value;
        } elseif ($offset === 1) {
            $this->right = $value;
        } else {
            throw $this->throwOffsetException($offset);
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        if ($offset === 0) {
            $this->left = null;
        } elseif ($offset === 1) {
            $this->right = null;
        } else {
            throw $this->throwOffsetException($offset);
        }
    }

    /**
     * @param $offset
     * @return InvalidArgumentTypeException|OutOfRangeException
     */
    protected function throwOffsetException($offset)
    {
        if (!is_int($offset)) {
            return new InvalidArgumentTypeException('Expected argument offset to be of type "int"');
        }

        return new OutOfRangeException(sprintf('Offset %s is out of range', $offset));
    }
}
