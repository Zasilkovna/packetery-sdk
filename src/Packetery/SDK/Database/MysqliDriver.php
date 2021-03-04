<?php

namespace Packetery\SDK\Database;

class MysqliDriver implements IDriver
{
    /** @var \mysqli */
    protected $connection;

    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql) ?: null;

        $error = mysqli_error($this->connection);
        if (!empty($error)) {
            throw new DriverException($error . "\n" . $sql);
        }

        if ($result === false) {
            throw new DriverException('Unknown error. Warnings: ' . mysqli_get_warnings($this->connection)->message);
        }

        return $result instanceof \mysqli_result ? new MysqliResult($result) : null;
    }

    public function connect(array $config)
    {
        $this->connection = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);

        if (mysqli_errno($this->connection)) {
            throw new DriverException(mysqli_error($this->connection));
        }

        mysqli_set_charset($this->connection, $config['charset']);
    }

    public function disconnect()
    {
        mysqli_close($this->connection);
    }

    public function escapeText($input)
    {
        return "'" . mysqli_real_escape_string($this->connection, $input) . "'";
    }

    public function escapeBool($input)
    {
        return $input ? 1 : 0;
    }

    /**
     * Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
     * @return int|FALSE  int on success or FALSE on failure
     */
    public function getInsertId($sequence)
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Begins a transaction (if supported).
     * @param  string  optional savepoint name
     * @return void
     */
    public function begin($savepoint = null)
    {
        $this->query($savepoint ? "SAVEPOINT $savepoint" : 'START TRANSACTION');
    }

    /**
     * Commits statements in a transaction.
     * @param  string  optional savepoint name
     * @return void
     */
    public function commit($savepoint = null)
    {
        $this->query($savepoint ? "RELEASE SAVEPOINT $savepoint" : 'COMMIT');
    }

    /**
     * Rollback changes in a transaction.
     * @param  string  optional savepoint name
     * @return void
     */
    public function rollback($savepoint = null)
    {
        $this->query($savepoint ? "ROLLBACK TO SAVEPOINT $savepoint" : 'ROLLBACK');
    }

    public function isConnected()
    {
        return $this->connection !== null;
    }
}
