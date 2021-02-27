<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

interface IFeedService
{
    /** Returns home delivery carriers by specified filter
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Traversable
     */
    public function getHDCarriers(BranchFilter $branchFilter = null);

    /**
     * @param string $id
     * @return mixed
     */
    public function getHDCarrierById($id);

    /**
     * @param string $country
     * @return \Traversable
     */
    public function getHDCarriersByCountry($country);
}
