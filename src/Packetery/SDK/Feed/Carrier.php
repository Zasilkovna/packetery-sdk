<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class Carrier
{
    /** @var StringVal */
    private $id;

    /** @var StringVal */
    private $name;

    /**
     * Carrier constructor.
     *
     * @param StringVal $id
     * @param StringVal $name
     */
    public function __construct(StringVal $id, StringVal $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromFeedArray(array $carrier)
    {
        return new static(StringVal::create((string)$carrier['id']), StringVal::create($carrier['name']));
    }

    /**
     * @return \Packetery\SDK\PrimitiveTypeWrapper\StringVal
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Packetery\SDK\PrimitiveTypeWrapper\StringVal
     */
    public function getName()
    {
        return $this->name;
    }
}
