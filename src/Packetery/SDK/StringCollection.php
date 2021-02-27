<?php

namespace Packetery\SDK;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class StringCollection extends Collection
{
    /**
     * @param string[] $strings
     * @return \Packetery\SDK\StringCollection
     */
    public static function createFromStrings(array $strings)
    {
        $collection = new self();

        foreach ($strings as $str) {
            $collection->add(new StringVal($str));
        }

        return $collection;
    }

    public function getItemClass()
    {
        return StringVal::class;
    }

    public function toValueArray()
    {
        return array_map(
            function (StringVal $val) {
                return $val->getValue();
            },
            $this->toArray()
        );
    }

    public function implode($glue)
    {
        return implode($glue, $this->toValueArray());
    }
}
