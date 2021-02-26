<?php

namespace Packetery\SDK\Database;

class Result implements \IteratorAggregate
{
    /** @var \Iterator */
    private $result;

    /**
     * Result constructor.
     *
     * @param \Traversable $result
     */
    public function __construct(\Iterator $result = null)
    {
        $this->result = $result;
    }

    public function fetch()
    {
        if (!empty($this->result)) {
            foreach ($this->result as $row) {
                return $row;
            }
        }

        return null;
    }

    public function isEmpty()
    {
        return empty($this->result);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->result);
    }
}
