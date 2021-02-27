<?php

namespace Packetery\SDK\Database;

class Transaction
{
    static $transaction_nest_level = 0;

    /** @var \Packetery\SDK\Database\IDriver  */
    private $driver;

    /** @var string name of savepoint */
    private $name;

    /** @var bool */
    private $finished;

    function __construct(IDriver $db)
    {
        $this->driver = $db;

        $this->name = 'T' . md5(microtime());
        if (self::$transaction_nest_level++ == 0) {
            $this->driver->begin();
        }
        $this->driver->begin($this->name);

        $this->finished = false;
    }

    function __destruct()
    {
        if (!$this->finished) {
            $this->rollback();
        }
    }

    function commit()
    {
        $this->driver->commit($this->name);
        if (--self::$transaction_nest_level == 0) {
            $this->driver->commit();
        }

        $this->finished = true;
    }

    function rollback()
    {
        $this->driver->rollback($this->name);
        if (--self::$transaction_nest_level == 0) {
            $this->driver->commit();
        }

        $this->finished = true;
    }
}
