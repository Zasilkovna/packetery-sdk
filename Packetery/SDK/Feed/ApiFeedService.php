<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\StringCollection;

class ApiFeedService implements IFeedService
{
    /** @var \Packetery\SDK\Feed\FeedServiceBrain */
    private $feedServiceBrain;

    /**
     * ApiFeedService constructor.
     *
     * @param \Packetery\SDK\Feed\FeedServiceBrain $feedServiceBrain
     */
    public function __construct(FeedServiceBrain $feedServiceBrain)
    {
        $this->feedServiceBrain = $feedServiceBrain;
    }

    /** Returns home delivery carriers
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection
     */
    public function getHDCarriers(BranchFilter $branchFilter = null)
    {
        $collection = new CarrierCollection();

        $count = 0;
        $limit = $branchFilter !== null && $branchFilter->getLimit() !== null && $branchFilter->getLimit()->getValue() > 0 ? $branchFilter->getLimit()->getValue() : null;
        $carriers = $this->feedServiceBrain->getHDCarrierGenerator();
        foreach ($carriers as $carrier) {
            if ($limit !== null && $count > $limit) {
                break;
            }

            if ($branchFilter->getIds() !== null) {
                if (!in_array($carrier->getId(), $branchFilter->getIds()->toValueArray())) {
                    continue;
                }
            }

            if ($branchFilter->getCountry() !== null) {
                if (!$branchFilter->getCountry()->equals($carrier->getCountry())) {
                    continue;
                }
            }

            $collection->add($carrier);
            $count++;
        }

        return $collection;
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function getHDCarrierById($id)
    {
        $filter = new BranchFilter();
        $filter->setIds(new StringCollection([$id]));
        $filter->setLimit(new IntVal(1));
        $collection = $this->getHDCarriers($filter);
        return $collection->first();
    }

    /**
     * @param $country
     * @return \Packetery\SDK\Feed\CarrierCollection
     */
    public function getHDCarriersByCountry($country)
    {
        $filter = new BranchFilter();
        $filter->setCountry(StringVal::parse($country));
        return $this->getHDCarriers($filter);
    }
}
