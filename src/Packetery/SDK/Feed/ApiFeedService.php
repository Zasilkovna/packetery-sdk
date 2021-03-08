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

                $carrierSample = $branchFilter->getCarrierSample();
                if ($carrierSample) {
                    if ($carrierSample->getCountry() !== null) {
                        if ($carrierSample->getCountry() !== $carrier->getCountry()) {
                            continue;
                        }
                    }

                    if ($carrierSample->isPickupPoints() !== null) {
                        if ($carrierSample->isPickupPoints() !== $carrier->isPickupPoints()) {
                            continue;
                        }
                    }

                    if ($carrierSample->isCustomsDeclarations() !== null) {
                        if ($carrierSample->isCustomsDeclarations() !== $carrier->isCustomsDeclarations()) {
                            continue;
                        }
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

        $sample = $filter->getCarrierSample() ?: new CarrierSample();
        $sample->setCountry($country);

        $filter->setCarrierSample($sample);
        return $this->getCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getAddressDeliveryCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = $branchFilter ?: new CarrierFilter();

        $sample = $filter->getCarrierSample() ?: new CarrierSample();
        $sample->setPickupPoints(false);

        $filter->setCarrierSample($sample);
        return $this->getCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getPickupPointCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = $branchFilter ?: new CarrierFilter();

        $sample = $filter->getCarrierSample() ?: new CarrierSample();
        $sample->setPickupPoints(true);

        $filter->setCarrierSample($sample);
        return $this->getCarriers($filter);
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getAddressDeliveryCarriersByCountry($country)
    {
        $filter = new CarrierFilter();

        $sample = new CarrierSample();
        $sample->setCountry($country);

        $filter->setCarrierSample($sample);
        return $this->getAddressDeliveryCarriers($filter);
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\CarrierCollection|\Traversable|\Packetery\SDK\Feed\Carrier[]
     */
    public function getPickupPointCarriersByCountry($country)
    {
        $filter = new CarrierFilter();

        $sample = new CarrierSample();
        $sample->setCountry($country);

        $filter->setCarrierSample($sample);
        return $this->getPickupPointCarriers($filter);
    }
}
