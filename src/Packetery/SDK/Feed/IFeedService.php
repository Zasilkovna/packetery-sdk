<?php

namespace Packetery\SDK\Feed;

interface IFeedService
{
    /** Returns home delivery carriers by specified filter
     *
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Traversable
     */
    public function getCarriers(CarrierFilter $branchFilter = null);

    /**
     * @param string $id
     * @return mixed
     */
    public function getCarrierById($id);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getCarriersByCountry($country);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getAddressDeliveryCarriers(CarrierFilter $branchFilter = null);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getAddressDeliveryCarriersByCountry($country);
}
