<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Database\Connection;

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

    /**
     * synces database with feed export data
     */
    public function updateData()
    {
        $generator = $this->feedServiceBrain->getSimpleCarrierGenerator(); // going to start new download
        foreach ($generator as $SimpleCarrier) {
            $this->connection->transactional(
                function () use ($SimpleCarrier) {
                    $filter = new CarrierFilter();
                    $filter->setIds(([$SimpleCarrier->getId()]));
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
        $branchFilter->setIds(([$id]));
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

        $sample = $branchFilter->getSimpleCarrierSample() ?: new SimpleCarrierSample();
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

        $sample = $branchFilter->getSimpleCarrierSample() ?: new SimpleCarrierSample();
        $sample->setPickupPoints(true);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }
}
