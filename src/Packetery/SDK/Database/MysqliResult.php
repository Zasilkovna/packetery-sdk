<?php

namespace Packetery\SDK\Database;

class MysqliResult implements IDriverResult
{
    /** @var \mysqli_result */
    private $resultSet;

    /** @var int|null */
    private $resultPointer = null;

    public function __construct(\mysqli_result $resultSet)
    {
        $this->resultSet = $resultSet;
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
        $this->resultPointer = 0;
        $this->resultSet->data_seek(0);
    }

    public function count()
    {
        return $this->resultSet->num_rows;
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
}
