<?php

namespace Packetery\SDK\Storage;

use Packetery\Domain\InvalidStateException;

class MemoryStorage implements IStorage
{
    /** @var array */
    private $data;

    function get($key)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return null;
    }

    function set($key, $content)
    {
        $this->data[$key] = $content;
    }

    function exists($key)
    {
        return isset($this->data[$key]);
    }

    function remove($key)
    {
        unset($this->data[$key]);
    }

    function duration($val)
    {
        throw new InvalidStateException('not implemented');
    }
}
