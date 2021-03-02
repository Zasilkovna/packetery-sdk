<?php

namespace Packetery\SDK\Feed;

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
     *
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\SimpleCarrierCollection|\Packetery\SDK\Feed\SimpleCarrier[]
     */
    public function getSimpleCarriers(CarrierFilter $branchFilter = null)
    {
        $collection = new SimpleCarrierCollection();

        $count = 0;
        $limit = $branchFilter !== null && $branchFilter->getLimit() !== null && $branchFilter->getLimit()->getValue() > 0 ? $branchFilter->getLimit()->getValue() : null;
        $carriers = $this->feedServiceBrain->getSimpleCarrierGenerator();
        foreach ($carriers as $carrier) {
            if ($limit !== null && $count > $limit) {
                break;
            }

            if ($branchFilter) {
                if ($branchFilter->getIds() !== null) {
                    if (!$branchFilter->getIds()->containsValue((string)$carrier->getId())) {
                        continue;
                    }
                }
                if ($branchFilter->getExcludedIds() !== null) {
                    if ($branchFilter->getExcludedIds()->containsValue((string)$carrier->getId())) {
                        continue;
                    }
                }

                $carrierSample = $branchFilter->getSimpleCarrierSample();
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
                }
            }

            $collection->add($carrier);
            $count++;
        }

        return $collection;
    }

    /**
     * @param $id
     * @return \Packetery\SDK\Feed\SimpleCarrier|null
     */
    public function getSimpleCarrierById($id)
    {
        $filter = new CarrierFilter();
        $filter->setIds(StringCollection::createFromStrings([$id]));
        $filter->setLimit(1);
        $collection = $this->getSimpleCarriers($filter);
        return $collection->first();
    }

    /**
     * @param $country
     * @return \Packetery\SDK\Feed\SimpleCarrierCollection|\Packetery\SDK\Feed\SimpleCarrier[]
     */
    public function getSimpleCarriersByCountry($country)
    {
        $filter = new CarrierFilter();

        $sample = $filter->getSimpleCarrierSample() ?: new SimpleCarrierSample(); // todo same with db feed
        $sample->setCountry($country);

        $filter->setSimpleCarrierSample($sample);
        return $this->getSimpleCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\SimpleCarrierCollection|\Traversable|\Packetery\SDK\Feed\SimpleCarrier[]
     */
    public function getHomeDeliveryCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = $branchFilter ?: new CarrierFilter();

        $sample = $filter->getSimpleCarrierSample() ?: new SimpleCarrierSample();
        $sample->setPickupPoints(false);

        $filter->setSimpleCarrierSample($sample);
        return $this->getSimpleCarriers($filter);
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\SimpleCarrierCollection|\Traversable|\Packetery\SDK\Feed\SimpleCarrier[]
     */
    public function getHomeDeliveryCarriersByCountry($country)
    {
        $filter = new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(false);
        $sample->setCountry($country);

        $filter->setSimpleCarrierSample($sample);
        return $this->getSimpleCarriers($filter);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\SimpleCarrierCollection|\Traversable|\Packetery\SDK\Feed\SimpleCarrier[]
     */
    public function getPickupPointCarriers(CarrierFilter $branchFilter = null)
    {
        $filter = $branchFilter ?: new CarrierFilter();

        $sample = $branchFilter->getSimpleCarrierSample() ?: new SimpleCarrierSample();
        $sample->setPickupPoints(true);

        $filter->setSimpleCarrierSample($sample);
        return $this->getSimpleCarriers($filter);
    }
}
