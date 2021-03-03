<?php

namespace Packetery\SDK\Storage;

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
        return 0.0; // todo implement correct durations
    }
}
