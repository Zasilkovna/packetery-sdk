<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Storage\FileStorage;

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
     *
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection|\Packetery\SDK\Feed\Carrier[]
     */
    public function getCarriers(CarrierFilter $branchFilter = null)
    {
        $collection = new CarrierCollection();

        $count = 0;
        $limit = ($branchFilter !== null ? $branchFilter->getLimit() : null);
        $carriers = $this->feedServiceBrain->getSimpleCarrierGenerator();
        foreach ($carriers as $carrier) {
            if ($limit !== null && $count >= $limit) {
                break;
            }

            if ($branchFilter) {
                if ($branchFilter->getIds() !== null) {
                    if (!in_array($carrier->getId(), $branchFilter->getIds())) {
                        continue;
                    }
                }

                if ($branchFilter->getCountries() !== null) {
                    if (!in_array($carrier->getCountry(), $branchFilter->getCountries())) {
                        continue;
                    }
                }

                if ($branchFilter->getHasPickupPoints() !== null) {
                    if ($carrier->isPickupPoints() !== $branchFilter->getHasPickupPoints()) {
                        continue;
                    }
                }

                if ($branchFilter->getRequiresCustomsDeclarations() !== null) {
                    if ($carrier->isCustomsDeclarations() !== $branchFilter->getRequiresCustomsDeclarations()) {
                        continue;
                    }
                }
            }

            $collection->add($carrier);
            $count++;
        }

        return $collection;
    }

    /**
     * @param $id
     * @return \Packetery\SDK\Feed\Carrier|null
     */
    public function getCarrierById($id)
    {
        $filter = new CarrierFilter();
        $filter->setIds(([$id]));
        $filter->setLimit(1);
        $collection = $this->getCarriers($filter);
        return $collection->first();
    }

    /**
     * @param $country
     * @return \Packetery\SDK\Feed\CarrierCollection|\Packetery\SDK\Feed\Carrier[]
     */
    public function getCarriersByCountry($country)
    {
        $filter = new CarrierFilter();
        $filter->setCountries([$country]);
        return $this->getCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getAddressDeliveryCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = ($branchFilter ?: new CarrierFilter());
        $filter->setHasPickupPoints(false);
        return $this->getCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getPickupPointCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = ($branchFilter ?: new CarrierFilter());
        $filter->setHasPickupPoints(true);
        return $this->getCarriers($filter);
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getAddressDeliveryCarriersByCountry($country)
    {
        $filter = new CarrierFilter();
        $filter->setCountries([$country]);
        return $this->getAddressDeliveryCarriers($filter);
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getPickupPointCarriersByCountry($country)
    {
        $filter = new CarrierFilter();
        $filter->setCountries([$country]);
        return $this->getPickupPointCarriers($filter);
    }
}
