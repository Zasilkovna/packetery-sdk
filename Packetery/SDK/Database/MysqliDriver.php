<?php

namespace Packetery\SDK\Database;

class MysqliDriver implements IDriver
{
    /** @var \mysqli */
    private $link;

    public function query($sql)
    {
        return new Result(mysqli_query($this->link, (string)$sql) ?: null);
    }

    public function connect(array $config)
    {
        $this->link = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
    }

    public function disconnect()
    {
        mysqli_close($this->link);
    }

    public function escape($input)
    {
        return mysqli_real_escape_string($this->link, $input);
    }

    public function begin() // todo transactional
    {
        mysqli_begin_transaction($this->link);
    }

    public function rollbak(callable $callback)
    {
        mysqli_rollback($this->link);
    }

    public function commit(callable $callback)
    {
        mysqli_commit($this->link);
    }
}
