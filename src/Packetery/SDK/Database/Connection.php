<?php

namespace Packetery\SDK\Database;

use Packetery\SDK\Config;
use Packetery\SDK\StringCollection;

class Connection
{
    /** @var IDriver */
    private $driver;

    /** @var Config */
    private $config;

    /** @var bool  */
    private $connected = false;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $driver = new MysqliDriver();
        $this->driver = $driver;
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
        return new Result($this->getDriver()->query((string)$sql));
    }

    public function escapeStringCollection(StringCollection $input)
    {
        $remapped = array_map(
            function ($value) {
                return $this->getDriver()->escapeText($value);
            },
            $input->toValueArray()
        );

        return StringCollection::createFromStrings($remapped);
    }

    public function escapeText($input)
    {
        return $this->getDriver()->escapeText((string)$input);
    }

    public function transactional(callable $callback)
    {
        $transaction = new Transaction($this->getDriver());

        try {
            call_user_func_array($callback, []);
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollback();
            throw $exception;
        }
    }
}
