<?php

namespace Packetery\SDK\Database;

interface IDriver extends \Iterator
{
    public function connect(array $config);
    public function disconnect();

    /**
     * @param $sql
     * @return \Packetery\SDK\Database\IDriver
     */
    public function query($sql);

    public function getInsertId($sequence);
    public function escapeText($input);
    public function getRowCount();
    public function commit($savepoint = null);
    public function rollback($savepoint = null);
    public function begin($savepoint = null);
    public function fetch($assoc);
    public function free();
}
