<?php

namespace Packetery\SDK\Database;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class Connection
{
    /** @var IDriver */
    private $driver;

    /**
     * Connection constructor.
     *
     * @param IDriver $driver
     */
    public function __construct(IDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $sql
     * @return \Packetery\SDK\Database\Result
     */
    public function query($sql)
    {
        return $this->driver->query((string)$sql);
    }

    public function escape($input)
    {
        return$this->driver->escape((string)$input);
    }
}
