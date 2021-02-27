<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

/** todo carrier has flag that says if it is HD or PP carrier and contains no list of points
 * Home delivery carrier
 */
class SimpleCarrier extends Carrier
{
    /** @var string|null */
    private $country;

    /** @var bool|null */
    private $pickupPoints;

    /** @var bool|null */
    private $apiAllowed;

    /** @var bool|null */
    private $separateHouseNumber;

    /** @var bool|null */
    private $customsDeclarations;

    /** @var bool|null */
    private $requiresEmail;

    /** @var bool|null */
    private $requiresPhone;

    /** @var bool|null */
    private $requiresSize;

    /** @var bool|null */
    private $disallowsCod;

    /** @var string|null */
    private $currency;

    /** @var string|null */
    private $maxWeight;

    /** @var string|null */
    private $labelRouting;

    /** @var string|null */
    private $labelName;

    public static function createFromArray(array $carrier)
    {
        $instance = parent::createFromArray($carrier);

        if (!empty($carrier['country'])) {
            $instance->setCountry($carrier['country']);
        }

        $instance->setApiAllowed(BoolVal::parse($carrier['apiAllowed'])->getValue());
        $instance->setPickupPoints(BoolVal::parse($carrier['pickupPoints'])->getValue());
        $instance->setCustomsDeclarations(BoolVal::parse($carrier['customsDeclarations'])->getValue());
        $instance->setRequiresEmail(BoolVal::parse($carrier['requiresEmail'])->getValue());
        $instance->setRequiresPhone(BoolVal::parse($carrier['requiresPhone'])->getValue());
        $instance->setRequiresSize(BoolVal::parse($carrier['requiresSize'])->getValue());
        $instance->setSeparateHouseNumber(BoolVal::parse($carrier['separateHouseNumber'])->getValue());
        $instance->setDisallowsCod(BoolVal::parse($carrier['disallowsCod'])->getValue());

        $instance->setCurrency($carrier['currency']);
        $instance->setLabelName($carrier['labelName']);
        $instance->setLabelRouting($carrier['labelRouting']);
        $instance->setMaxWeight($carrier['maxWeight']);

        return $instance;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country = null)
    {
        $this->country = $country;
    }

    /**
     * @return bool
     */
    public function isPickupPoints()
    {
        return $this->pickupPoints;
    }

    public function setPickupPoints($pickupPoints = null)
    {
        $this->pickupPoints = $pickupPoints;
    }

    /**
     * @return bool
     */
    public function isApiAllowed()
    {
        return $this->apiAllowed;
    }

    public function setApiAllowed($apiAllowed = null)
    {
        $this->apiAllowed = $apiAllowed;
    }

    /**
     * @return bool
     */
    public function isSeparateHouseNumber()
    {
        return $this->separateHouseNumber;
    }

    public function setSeparateHouseNumber($separateHouseNumber = null)
    {
        $this->separateHouseNumber = $separateHouseNumber;
    }

    /**
     * @return bool
     */
    public function isCustomsDeclarations()
    {
        return $this->customsDeclarations;
    }

    public function setCustomsDeclarations($customsDeclarations = null)
    {
        $this->customsDeclarations = $customsDeclarations;
    }

    /**
     * @return bool
     */
    public function isRequiresEmail()
    {
        return $this->requiresEmail;
    }

    public function setRequiresEmail($requiresEmail = null)
    {
        $this->requiresEmail = $requiresEmail;
    }

    /**
     * @return bool
     */
    public function isRequiresPhone()
    {
        return $this->requiresPhone;
    }

    public function setRequiresPhone($requiresPhone = null)
    {
        $this->requiresPhone = $requiresPhone;
    }

    /**
     * @return bool
     */
    public function isRequiresSize()
    {
        return $this->requiresSize;
    }

    public function setRequiresSize($requiresSize = null)
    {
        $this->requiresSize = $requiresSize;
    }

    /**
     * @return bool
     */
    public function isDisallowsCod()
    {
        return $this->disallowsCod;
    }

    public function setDisallowsCod($disallowsCod = null)
    {
        $this->disallowsCod = $disallowsCod;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency = null)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    public function setMaxWeight($maxWeight = null)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * @return string
     */
    public function getLabelRouting()
    {
        return $this->labelRouting;
    }

    public function setLabelRouting($labelRouting = null)
    {
        $this->labelRouting = $labelRouting;
    }

    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->labelName;
    }

    public function setLabelName($labelName = null)
    {
        $this->labelName = $labelName;
    }
}
