<?php
declare(strict_types=1);

namespace Iresults\Collection;

use Iterator;
use UnexpectedValueException;

/**
 * Object-to-object data store
 */
class Map implements Iterator, MapInterface
{
    /**
     * Map of the object hash to the key object
     *
     * @var array
     */
    private $hashToKeyObjectMap = [];

    /**
     * Map of the object hash to the value object
     *
     * @var array
     */
    private $hashToValueMap = [];

    /**
     * Map constructor.
     *
     * @param array $objects
     */
    public function __construct(array $objects = [])
    {
        foreach ($objects as $objectAndValue) {
            $this->assertPair($objectAndValue);
            $this->offsetSet($objectAndValue[0], $objectAndValue[1]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public static function withPairs(...$pairs): MapInterface
    {
        foreach ($pairs as $pair) {
            static::assertPair($pair);
        }

        return new static($pairs);
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        return $this->hashToValueMap;
    }

    /**
     * @inheritdoc
     */
    public function getKeys(): array
    {
        return $this->hashToKeyObjectMap;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        $currentKey = $this->hashKey();
        if (isset($this->hashToValueMap[$currentKey])) {
            return $this->hashToValueMap[$currentKey];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->hashToKeyObjectMap);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return current($this->hashToKeyObjectMap);
    }

    /**
     * @inheritdoc
     */
    public function hashKey()
    {
        return key($this->hashToKeyObjectMap);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return (current($this->hashToKeyObjectMap) !== false);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->hashToKeyObjectMap);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->hashToKeyObjectMap[$this->hash($offset)]);
    }

    /**
     * @inheritdoc
     */
    public function exists($keyObject): bool
    {
        return $this->offsetExists($keyObject);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        return $this->hashToValueMap[$this->hash($offset)];
    }

    /**
     * @inheritdoc
     */
    public function get($keyObject)
    {
        return $this->offsetGet($keyObject);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $hash = $this->hash($offset);
        $this->hashToKeyObjectMap[$hash] = $offset;
        $this->hashToValueMap[$hash] = $value;
    }

    /**
     * @inheritdoc
     */
    public function set($keyObject, $value)
    {
        $this->offsetSet($keyObject, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $hash = $this->hash($offset);
        unset($this->hashToKeyObjectMap[$hash]);
        unset($this->hashToValueMap[$hash]);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->hashToKeyObjectMap);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $callback): CollectionInterface
    {
        $result = new static();
        foreach ($this as $keyObject => $value) {
            $result->offsetSet($keyObject, $callback($keyObject, $value));
        }

        return $result;
    }

    public function find(callable $callback)
    {
        foreach ($this as $keyObject => $value) {
            if ($callback($value)) {
                return $value;
            }
        }

        return null;
    }


    public function findPair(callable $callback)
    {
        foreach ($this as $keyObject => $value) {
            $pair = new Pair($keyObject, $value);
            if ($callback($pair)) {
                return $pair;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $callback, $flag = 0): CollectionInterface
    {
        $result = new static();
        foreach ($this as $keyObject => $value) {
            if ($callback($keyObject, $value)) {
                $result->offsetSet($keyObject, $value);
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function implode($glue = ''): string
    {
        return implode($glue, $this->getArrayCopy());
    }


    /**
     * @param string|object $variable
     * @return string
     */
    protected function hash($variable)
    {
        if (is_string($variable)) {
            return $variable;
        }
        if (is_object($variable)) {
            return spl_object_hash($variable);
        }
        throw new UnexpectedValueException(
            sprintf('Can not create hash for variable of type "%s"', gettype($variable)),
            1442825536
        );
    }

    /**
     * @param array $objectAndValue
     * @return void
     */
    private static function assertPair($objectAndValue)
    {
        if (!is_array($objectAndValue)) {
            throw new \InvalidArgumentException('Constructor argument must be an array of arrays', 1442827041);
        }
        if (!isset($objectAndValue[0]) || count($objectAndValue) < 2) {
            throw new \InvalidArgumentException('Constructor argument must be an array of arrays', 1442827041);
        }
    }
}
