<?php

namespace Packetery\SDK\Feed;

use Packetery\Utils\Arrays;

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

    private static function parseBool($carrier, $keys)
    {
        $value = Arrays::getValue($carrier, $keys, null);
        if ($value === null) {
            return null;
        }

        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }

        return (bool) $value;
    }

    public static function createFromFeedArray(array $carrier)
    {
        $instance = parent::createFromFeedArray($carrier);

        $instance->setApiAllowed(self::parseBool($carrier, ['apiAllowed']));
        $instance->setPickupPoints(self::parseBool($carrier, ['pickupPoints']));
        $instance->setCustomsDeclarations(self::parseBool($carrier, ['customsDeclarations']));
        $instance->setRequiresEmail(self::parseBool($carrier, ['requiresEmail']));
        $instance->setRequiresPhone(self::parseBool($carrier, ['requiresPhone']));
        $instance->setRequiresSize(self::parseBool($carrier, ['requiresSize']));
        $instance->setSeparateHouseNumber(self::parseBool($carrier, ['separateHouseNumber']));
        $instance->setDisallowsCod(self::parseBool($carrier, ['disallowsCod']));

        $instance->setCountry(Arrays::getValue($carrier, ['country'], null));
        $instance->setCurrency(Arrays::getValue($carrier, ['currency'], null));
        $instance->setLabelName(Arrays::getValue($carrier, ['labelName'], null));
        $instance->setLabelRouting(Arrays::getValue($carrier, ['labelRouting'], null));
        $instance->setMaxWeight(Arrays::getValue($carrier, ['maxWeight'], null));

        return $instance;
    }

    public static function createFromDatabaseRow(array $carrier)
    {
        if (empty($carrier['id'])) {
            $carrier['id'] = $carrier['carrier_id']; // always same thing
        }
        unset($carrier['carrier_id']);
        return self::createFromFeedArray($carrier);
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
