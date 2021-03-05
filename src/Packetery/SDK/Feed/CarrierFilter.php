<?php

namespace Packetery\SDK\Feed;

/**
 * Allows high abstraction level to specify what should be returned
 */
class CarrierFilter
{
    /** @var array|null */
    private $ids;

    /** @var array|null */
    private $excludedIds;

    /** @var int|null */
    private $limit;

    /** @var \Packetery\SDK\Feed\SimpleCarrierSample|null */
    private $simpleCarrierSample;

    /**
     * @return array|null
     */
    public function getExcludedIds()
    {
        return $this->excludedIds;
    }

    public function setExcludedIds(array $excludedIds = null)
    {
        $this->excludedIds = $excludedIds;
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrierSample|null
     */
    public function getSimpleCarrierSample()
    {
        return $this->simpleCarrierSample;
    }

    public function setSimpleCarrierSample(SimpleCarrierSample $simpleCarrierSample = null)
    {
        $this->simpleCarrierSample = $simpleCarrierSample;
    }

    /** null means that you do not care
     *
     * @param string $country
     * @param bool $forAddressDelivery Only select carriers for home delivery?
     */
    public function buildSample($country, $forAddressDelivery)
    {
        $sample = new SimpleCarrierSample();

        if (is_string($country)) {
            $sample->setCountry($country);
        }

        if (is_bool($forAddressDelivery)) {
            $sample->setPickupPoints(!$forAddressDelivery);
        }

        $this->simpleCarrierSample = $sample;
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
