<?php

namespace Packetery\SDK\Database;

interface IDriver
{
    public function connect(array $config);
    public function disconnect();

    /**
     * @param $sql
     * @return \IDriverResult
     */
    public function query($sql);

    public function getInsertId($sequence);
    public function escapeText($input);
    public function escapeBool($input);
    public function commit($savepoint = null);
    public function rollback($savepoint = null);
    public function begin($savepoint = null);
    public function isConnected();
}
