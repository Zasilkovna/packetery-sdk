<?php

namespace Packetery\SDK\Database;

use Packetery\Domain\InvalidArgumentException;

class Result implements \IteratorAggregate, \Countable
{
    /** @var IDriverResult */
    private $result;

    public function __construct(IDriverResult $result)
    {
        $this->result = $result;
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function getIterator()
    {
        return $this->result;
    }

    public function count()
    {
        return $this->result->count();
    }
}
