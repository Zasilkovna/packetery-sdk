<?php

namespace Packetery\SDK\Storage;

use Packetery\Domain\InvalidStateException;

/**
 * Temporary storage for primitive key/value pairs
 */
class MemoryStorage implements IStorage
{
    /** @var array */
    private $data;

    /**
     * @param string $key
     * @return mixed|string|null
     */
    function get($key)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param string $content
     */
    function set($key, $content)
    {
        $this->data[$key] = $content;
    }

    /**
     * @param string $key
     * @return bool
     */
    function exists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     */
    function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * @param string $val
     * @return float|void|null
     * @throws \Packetery\Domain\InvalidStateException
     */
    function duration($val)
    {
        throw new InvalidStateException('not implemented');
    }
}
