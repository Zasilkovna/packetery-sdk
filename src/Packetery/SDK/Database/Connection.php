<?php

namespace Packetery\SDK\Database;

use Packetery\SDK\StringCollection;

class Connection
{
    /** @var IDriver */
    private $driver;

    /**
     * Connection constructor.
     *
     * @param IDriver $driver
     */
    public function __construct(IDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $sql
     * @return \Packetery\SDK\Database\Result
     */
    public function query($sql)
    {
        return new Result($this->driver->query((string)$sql));
    }

    public function escapeStringCollection(StringCollection $input)
    {
        $remapped = array_map(
            function ($value) {
                return $this->driver->escapeText($value);
            },
            $input->toValueArray()
        );

        return StringCollection::createFromStrings($remapped);
    }

    public function escapeText($input)
    {
        return $this->driver->escapeText((string)$input);
    }

    public function transactional(callable $callback)
    {
        $transaction = new Transaction($this->driver);

        try {
            call_user_func_array($callback, []);
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollback();
            throw $exception;
        }
    }
}
