<?php

namespace Packetery\SDK\Database;

use Exception;
use Packetery\SDK\Config;

class Connection
{
    /** @var IDriver */
    private $driver;

    /** @var Config */
    private $config;

    /** @var bool */
    private $connected;

    public function __construct(Config $config, IDriver $driver)
    {
        $this->config = $config;
        $this->driver = $driver;
        $this->connected = $driver->isConnected();
    }

    public function connect()
    {
        $this->driver->connect($this->config->getConnection());
        $this->connected = true;
    }

    private function getDriver()
    {
        $this->connected || $this->connect();
        return $this->driver;
    }

    /**
     * @param $sql
     * @return \Packetery\SDK\Database\Result
     */
    public function query($sql)
    {
        return new Result($this->getDriver()->query((string)$sql) ?: new ArrayDriverResult([]));
    }

    public function escapeStringCollection(array $input)
    {
        $remapped = array_map(
            function ($value) {
                return $this->getDriver()->escapeText($value);
            },
            $input
        );

        return $remapped;
    }

    public function escapeText($input, $returnNullString = true)
    {
        if ($returnNullString && $input === null) {
            return 'null';
        }

        return $this->getDriver()->escapeText((string)$input);
    }

    public function escapeBool($input, $returnNullString = true)
    {
        if ($returnNullString && $input === null) {
            return 'null';
        }

        return $this->getDriver()->escapeBool((string)$input);
    }

    public function transactional(callable $callback)
    {
        // todo will it work for random drivers?
        $transaction = new Transaction($this->getDriver());

        try {
            call_user_func_array($callback, []);
            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollback();
            throw $exception;
        }
    }

    public function __destruct()
    {
        if ($this->connected) {
            $this->getDriver()->disconnect();
        }
    }
}
