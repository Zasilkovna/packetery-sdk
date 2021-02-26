<?php

namespace Packetery\Utils;

use Packetery\Domain\InvalidArgumentException;

class Arrays
{

    /** Returns assoc array value by given key sequence and avoids NOTICEs
     * @param array $arr
     * @param array $keys
     * @param null $default
     * @return array|mixed|null
     */
    public static function getValue(array $arr, array $keys, $default = null)
    {
        $value = $arr;
        $any = false;

        foreach ($keys as $key) {
            if (is_array($value) && isset($value[$key])) {
                $value = $arr[$key];
                $any = true;
            } else {
                // key does not exist in array
                $any = false;
            }
        }

        if ($any === false) {
            if (func_num_args() < 3) {
                throw new InvalidArgumentException('key sequence ' . implode('.', $keys) . ' was not found in array');
            } else {
                return $default;
            }
        }

        return $value;
    }
}
