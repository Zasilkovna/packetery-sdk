<?php

namespace Packetery\SDK\Database;

interface IDriver
{
    public function connect(array $config);
    public function disconnect();

    /**
     * @param $sql
     * @return \Packetery\SDK\Database\Result
     */
    public function query($sql);

    public function escape($input);
}
