<?php

namespace Packetery\SDK\Feed;

interface IFeedService
{
    /** Returns home delivery carriers by specified filter
     *
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Traversable
     */
    public function getSimpleCarriers(CarrierFilter $branchFilter = null);

    /**
     * @param string $id
     * @return mixed
     */
    public function getSimpleCarrierById($id);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getSimpleCarriersByCountry($country);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getHomeDeliveryCarriers(CarrierFilter $branchFilter = null);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getHomeDeliveryCarriersByCountry($country);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getPickupPointCarriers(CarrierFilter $branchFilter = null);
}
