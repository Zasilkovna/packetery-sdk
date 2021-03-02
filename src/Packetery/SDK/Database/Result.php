<?php

namespace Packetery\SDK\Database;

class Result implements \IteratorAggregate
{
    /** @var \Packetery\SDK\Database\IDriver */
    private $resultDriver;

    public function __construct(IDriver $driver)
    {
        $this->resultDriver = $driver;
    }

    public function isEmpty()
    {
        return empty($this->resultDriver->getIterator());
    }

    public function getIterator()
    {
        return $this->resultDriver->getIterator();
    }
}
