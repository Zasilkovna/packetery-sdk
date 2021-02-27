<?php

namespace Packetery\SDK\Database;

class MysqliDriver implements IDriver
{
    /** @var \mysqli */
    private $connection;

    /** @var \mysqli_result */
    private $resultSet;

    /** @var int|null */
    private $resultPointer = null;

    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql) ?: null;

        $error = mysqli_error($this->connection);
        if (!empty($error)) {
            throw new DriverException($error);
        }

        $resultDriver = clone $this;
        $resultDriver->resultSet = $result;
        return $resultDriver;
    }

    public function getRowCount()
    {
        return mysqli_affected_rows($this->connection);
    }

    public function connect(array $config)
    {
        $this->connection = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
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

    /**
     * Fetches the row at current position and moves the internal cursor to the next position.
     * @param  bool     TRUE for associative array, FALSE for numeric
     * @return array    array on success, nonarray if no next record
     */
    public function fetch($assoc)
    {
        return mysqli_fetch_array($this->resultSet, $assoc ? MYSQLI_ASSOC : MYSQLI_NUM);
    }

    /**
     * Frees the resources allocated for this result set.
     * @return void
     */
    public function free()
    {
        if ($this->resultSet instanceof \mysqli_result) {
            mysqli_free_result($this->resultSet);
        }

        $this->resultSet = null;
    }

    public function __destruct()
    {
        $this->free();
    }

    public function getIterator()
    {
        return $this->resultSet ? $this : new \ArrayIterator([]);
    }

    public function current()
    {
        if ($this->resultPointer === null) {
            $this->next();
        }

        return $this->resultSet->fetch_assoc();
    }

    public function next()
    {
        if ($this->resultPointer === null) {
            $this->resultPointer = 0;
        } else {
            $this->resultPointer++;
        }

        $this->resultSet->data_seek($this->resultPointer);
    }

    public function key()
    {
        return $this->resultPointer;
    }

    public function valid()
    {
        return $this->resultPointer === null || $this->resultSet->num_rows > $this->resultPointer;
    }

    public function rewind()
    {
        $this->resultSet->data_seek(0);
    }
}
