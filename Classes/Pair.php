<?php

namespace Iresults\Collection;


use Iresults\Collection\Exception\InvalidArgumentTypeException;
use Iresults\Collection\Exception\OutOfRangeException;

class Pair implements \ArrayAccess
{
    private $left;
    private $right;

    public function __construct($field1, $field2)
    {
        $this->left = $field1;
        $this->right = $field2;
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

    public function offsetExists($offset)
    {
        return is_int($offset) && $offset >= 0 && $offset < 2;
    }

    public function offsetGet($offset)
    {
        if ($offset === 0) {
            return $this->left;
        } elseif ($offset === 1) {
            return $this->right;
        }
        throw $this->throwOffsetException($offset);
    }

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
