<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

/**
 * Sample for BranchFilter
 */
class SimpleCarrierSample extends SimpleCarrier
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $name;

    public function __construct()
    {
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name = null)
    {
        $this->name = $name;
    }
}
