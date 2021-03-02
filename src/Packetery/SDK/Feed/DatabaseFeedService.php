<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Database\Connection;
use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\StringCollection;

class DatabaseFeedService implements IFeedService
{
    /** @var \Packetery\SDK\Database\Connection */
    private $connection;

    /** @var \Packetery\SDK\Feed\DatabaseRepository */
    public $repository;

    /** @var \Packetery\SDK\Feed\FeedServiceBrain */
    private $feedServiceBrain;

    public function __construct(Connection $connection, FeedServiceBrain $feedServiceBrain, DatabaseRepository $databaseRepository)
    {
        $this->connection = $connection;
        $this->feedServiceBrain = $feedServiceBrain;
        $this->repository = $databaseRepository;
    }

    private function isUpdateNeeded(CarrierFilter $branchFilter = null)
    {
        return !$this->feedServiceBrain->isSimpleCarrierFeedCached($branchFilter) || $this->feedServiceBrain->isSimpleCarrierFeedExpired($branchFilter);
    }

    private function updateData(CarrierFilter $branchFilter = null)
    {
        $generator = $this->feedServiceBrain->getSimpleCarrierGenerator($branchFilter); // going to start new download if isUpdateNeeded()
        foreach ($generator as $SimpleCarrier) {
            $this->connection->transactional(
                function () use ($SimpleCarrier) {
                    $filter = new CarrierFilter();
                    $filter->setIds(StringCollection::createFromStrings([$SimpleCarrier->getId()->getValue()]));
                    $result = $this->repository->findCarriers($filter);

                    // todo multi insert?
                    if (!$result->isEmpty()) {
                        $this->repository->updateCarrier($SimpleCarrier);
                    } else {
                        $this->repository->insertCarrier($SimpleCarrier);
                    }
                }
            );
        }
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\SimpleCarrierIterator
     * @throws \Exception
     */
    public function getSimpleCarriers(CarrierFilter $branchFilter = null)
    {
        if ($this->isUpdateNeeded($branchFilter)) {
            $this->updateData($branchFilter);
        }

        $result = $this->repository->findCarriers($branchFilter);
        return new SimpleCarrierIterator($result->getIterator());
    }

    /**
     * @param string $id
     * @return \Packetery\SDK\Feed\SimpleCarrier
     */
    public function getSimpleCarrierById($id)
    {
        $branchFilter = new CarrierFilter();
        $branchFilter->setIds(StringCollection::createFromStrings([$id]));
        $branchFilter->setLimit(1);
        return $this->getSimpleCarriers($branchFilter)->first();
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\SimpleCarrierIterator|\Traversable
     * @throws \Exception
     */
    public function getSimpleCarriersByCountry($country)
    {
        $branchFilter = new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setCountry($country);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }

    public function getHomeDeliveryCarriers(CarrierFilter $branchFilter = null)
    {
        $branchFilter = $branchFilter ?: new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(false);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }

    /**
     * @param $country
     * @return \Packetery\SDK\Feed\SimpleCarrierIterator
     * @throws \Exception
     */
    public function getHomeDeliveryCarriersByCountry($country)
    {
        $branchFilter = new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(false);
        $sample->setCountry($country);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }

    public function getPickupPointCarriers(CarrierFilter $branchFilter = null)
    {
        $branchFilter = $branchFilter ?: new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(true);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }
}
