<?php

namespace Packetery\SDK\Storage;

interface IStorage
{
    /**
     * @param string $key
     * @return string|null return null if content does not exist
     */
    function get($key);

    /**
     * @param string $key
     * @param string $content
     * @return void
     */
    function set($key, $content);

    /**
     * @param string $key
     * @return bool
     */
    function exists($key);

    /**
     * @param string $key
     * @return void
     */
    function remove($key);

    /**
     * @param string $val
     * @return float|null
     */
    function duration($val);
}
