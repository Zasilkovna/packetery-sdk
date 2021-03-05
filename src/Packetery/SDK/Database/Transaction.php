<?php

namespace Packetery\SDK\Database;

/**
 * Handles nested transactions
 */
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
        } else {
            $this->driver->begin($this->name);
        }

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
        if (--self::$transaction_nest_level == 0) {
            $this->driver->commit();
        } else {
            $this->driver->commit($this->name);
        }

        $this->finished = true;
    }

    function rollback()
    {
        if (--self::$transaction_nest_level == 0) {
            $this->driver->commit();
        } else {
            $this->driver->rollback($this->name);
        }

        $this->finished = true;
    }
}
