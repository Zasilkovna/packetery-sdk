<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidArgumentException;

abstract class Collection implements \JsonSerializable, \IteratorAggregate
{

    /**
     * @var array $items
     */
    protected $items = [];

    /**
     * ArrayCollection constructor.
     * @param array $items
     */
    public function __construct(array $items = []) {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     * @throws InvalidArgumentException
     */
    protected function onBeforeSet($key, $value) {
        $this->assertItemType($value);
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    protected function assertItemType($value) {
        $class = $this->getItemClass();
        if (!$value instanceof $class) {
            throw new InvalidArgumentException(sprintf("Invalid item type encoutered: '%s' expected '%s'.", get_class($value), $class));
        }
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    protected function onBeforeAdd($value) {
        $this->assertItemType($value);
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    public function add($value) {
        $this->onBeforeAdd($value);
        $this->items[] = $value;
    }

    /**
     * @param $key
     * @param $value
     * @throws InvalidArgumentException
     */
    public function set($key, $value) {
        $this->onBeforeSet($key, $value);
        $this->items[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function remove($key) {
        if($this->keyExists($key) === false) {
            return null;
        }
        $removed = $this->get($key);
        unset($this->items[$key]);

        return $removed;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key) {
        if($this->keyExists($key) === false) {
            return null;
        }

        return $this->items[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function keyExists($key) {
        if(!array_key_exists($key, $this->items)) {
            return false;
        }

        return true;
    }

    /**
     * Tells whether or not is this collection empty
     * @return bool
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /**
     * Retrieve an external iterator
     * @link  https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() {
        return new \ArrayIterator($this->items);
    }

    /**
     * Whether a offset exists
     * @link  https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Offset to retrieve
     * @link  https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link  https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @return void
     * @since 5.0.0
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value) {
        if($offset === null) {
            $this->add($value);
            return;
        }

        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link  https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    /**
     * Count elements of an object
     * @link  https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() {
        return count($this->items);
    }

    /**
     * Used to prepare data for json serialization
     *
     * @return array|mixed
     */
    public function jsonSerialize() {
        return $this->items;
    }


    /**
     * Returns the first element in this collection or null if empty
     *
     * @return mixed|null
     */
    public function first() {
        if($this->isEmpty()) {
            return null;
        }
        $itemsCopy = array_values($this->items); // Copy array values to preserve the internal array pointer

        return reset($itemsCopy);
    }

    /**
     * Returns the last element in this collection or null if empty
     *
     * @return mixed|null
     */
    public function last() {
        if($this->isEmpty()) {
            return null;
        }
        $itemsCopy = array_values($this->items); // Copy array values to preserve the internal array pointer

        return end($itemsCopy);
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->items;
    }

    /**
     * Filter items by value, leaving the original collection untouched.
     *
     * @param callable $filter
     * @return $this
     */
    public function filterByValue(callable $filter) {
        return new static(array_filter($this->items, $filter));
    }

    /**
     * Filter items by key, leaving the original collection untouched.
     *
     * @param callable $filter
     * @return $this
     */
    public function filterByKey(callable $filter) {
        return new static(array_filter($this->items, $filter, ARRAY_FILTER_USE_KEY));
    }

    /**
     * Filter items by key and value, leaving the original collection untouched.
     *
     * @param callable $filter
     * @return $this
     */
    public function filterByKeyAndValue(callable $filter) {
        return new static(array_filter($this->items, $filter, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Sort items by values, returning a copy of collection keeping the original untouched.
     *
     * @param callable|null $filter
     * @return $this
     */
    public function sortByValue($filter = null) {
        $items = array_replace([], $this->items);
        if($filter !== null) {
            uasort($items, $filter);
        }
        else {
            asort($items);
        }

        return new static($items);
    }

    /**
     * Sort items by its keys, returning a copy of collection keeping the original untouched.
     *
     * @param callable|null $filter
     * @return $this
     */
    public function sortByKey($filter = null) {
        $items = array_replace([], $this->items);
        if($filter !== null) {
            uksort($items, $filter);
        }
        else {
            ksort($items);
        }

        return new static($items);
    }

    /**
     * Return the first occurrence of an item matching given filter.
     *
     * @param callable $filter
     * @return mixed|null
     */
    public function findValue(callable $filter) {
        list(, $value) = $this->findKeyAndValue($filter) ?: [null, null];

        return $value;
    }

    /**
     * Return the key of the first occurrence of an item matching given filter.
     *
     * @param callable $filter
     * @return mixed|null
     */
    public function findKey(callable $filter) {
        list($key,) = $this->findKeyAndValue($filter) ?: [null, null];

        return $key;
    }

    /**
     * Return both the key and the value of an item matching given filter.
     *
     * @param callable $filter
     * @return array|null
     */
    public function findKeyAndValue(callable $filter) {
        foreach($this->items as $key => $item) {
            if($filter($item)) {
                return [$key, $item];
            }
        }

        return null;
    }

    /**
     * Return class of an Item
     *
     * @return string
     */
    abstract public function getItemClass();
}
