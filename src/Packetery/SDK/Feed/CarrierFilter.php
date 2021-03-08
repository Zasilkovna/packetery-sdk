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

    /** @var \Packetery\SDK\Feed\CarrierSample|null */
    private $carrierSample;

    /**
     * @return \Packetery\SDK\Feed\CarrierSample|null
     */
    public function getCarrierSample()
    {
        return $this->carrierSample;
    }

    public function setCarrierSample(CarrierSample $carrierSample = null)
    {
        $this->carrierSample = $carrierSample;
    }

    /** null means that you do not care
     *
     * @param string $country
     * @param bool $forAddressDelivery Only select carriers for home delivery?
     */
    public function buildSample($country, $forAddressDelivery)
    {
        $sample = new CarrierSample();

        if (is_string($country)) {
            $sample->setCountry($country);
        }

        if (is_bool($forAddressDelivery)) {
            $sample->setPickupPoints(!$forAddressDelivery);
        }

        $this->carrierSample = $sample;
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
