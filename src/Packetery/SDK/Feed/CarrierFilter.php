<?php

namespace Packetery\SDK\Feed;

/**
 * Allows high abstraction level to specify what should be returned
 */
class CarrierFilter
{
    /** @var array|null */
    private $ids;

    /** @var int|null */
    private $limit;

    /** @var string[] */
    private $countries;

    /** @var bool|null */
    private $requiresCustomsDeclarations;

    /** @var bool|null */
    private $hasPickupPoints;

    /**
     * @return string[]
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @param string[] $countries
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
    }

    /**
     * @return bool|null
     */
    public function getRequiresCustomsDeclarations()
    {
        return $this->requiresCustomsDeclarations;
    }

    /**
     * @param bool $requiresCustomsDeclarations
     */
    public function setRequiresCustomsDeclarations($requiresCustomsDeclarations)
    {
        $this->requiresCustomsDeclarations = $requiresCustomsDeclarations;
    }

    /**
     * @return bool|null
     */
    public function getHasPickupPoints()
    {
        return $this->hasPickupPoints;
    }

    /**
     * @param bool $hasPickupPoints
     */
    public function setHasPickupPoints($hasPickupPoints)
    {
        $this->hasPickupPoints = $hasPickupPoints;
    }

    /** null means that you do not care
     *
     * @param string $country
     * @param bool $forAddressDelivery Only select carriers for home delivery?
     */
    public function buildSample($country, $forAddressDelivery)
    {
        if (is_string($country)) {
            $this->setCountries([$country]);
        }

        if (is_bool($forAddressDelivery)) {
            $this->setHasPickupPoints(!$forAddressDelivery);
        }
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit = null)
    {
        $this->limit = $limit;
    }

    /**
     * @return array|null
     */
    public function getIds()
    {
        return $this->ids;
    }

    public function setIds(array $ids = null)
    {
        $this->ids = $ids;
    }
}
