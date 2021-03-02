<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

interface IFeedService
{
    /** Returns home delivery carriers by specified filter
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Traversable
     */
    public function getSimpleCarriers(BranchFilter $branchFilter = null);

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
    public function getHomeDeliveryCarriers(BranchFilter $branchFilter = null);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getHomeDeliveryCarriersByCountry($country);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getPickupPointCarriers(BranchFilter $branchFilter = null);
}
