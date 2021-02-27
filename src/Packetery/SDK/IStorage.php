<?php

namespace Packetery\SDK;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

interface IStorage
{
    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $key
     * @return StringVal
     */
    function get(StringVal $key);

    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $key
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $content
     * @return void
     */
    function set(StringVal $key, StringVal $content);

    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $key
     * @return bool
     */
    function exists(StringVal $key);

    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $key
     * @return void
     */
    function remove(StringVal $key);

    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $val
     * @return \Packetery\SDK\Duration
     */
    function duration(StringVal $val);
}
