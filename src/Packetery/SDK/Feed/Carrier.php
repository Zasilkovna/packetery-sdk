<?php

namespace Packetery\SDK\Feed;

class Carrier
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /**
     * Carrier constructor.
     *
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromFeedArray(array $carrier)
    {
        return new static(((string)$carrier['id']), ($carrier['name']));
    }

    /**
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }
}
