<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

/**
 * Home delivery carrier
 */
class HDCarrier extends Carrier
{
    /** @var StringVal */
    private $country;

    /**
     * @return \Packetery\SDK\PrimitiveTypeWrapper\StringVal
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(StringVal $country)
    {
        $this->country = $country;
    }

    public static function createFromArray(array $carrier)
    {
        $instance = parent::createFromArray($carrier);

        if (!empty($carrier['country'])) {
            $instance->setCountry(StringVal::create($carrier['country']));
        }

        return $instance;
    }
}
