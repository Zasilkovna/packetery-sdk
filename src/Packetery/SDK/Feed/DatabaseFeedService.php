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

    private function isUpdateNeeded(BranchFilter $branchFilter = null)
    {
        return !$this->feedServiceBrain->isSimpleCarrierFeedCached($branchFilter) || $this->feedServiceBrain->isSimpleCarrierFeedExpired($branchFilter);
    }

    private function updateData(BranchFilter $branchFilter = null)
    {
        $generator = $this->feedServiceBrain->getSimpleCarrierGenerator($branchFilter); // going to start new download if isUpdateNeeded()
        foreach ($generator as $SimpleCarrier) {
            $this->connection->transactional(
                function () use ($SimpleCarrier) {
                    $filter = new BranchFilter();
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
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\SimpleCarrierIterator
     * @throws \Exception
     */
    public function getSimpleCarriers(BranchFilter $branchFilter = null)
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
        $branchFilter = new BranchFilter();
        $branchFilter->setIds(StringCollection::createFromStrings([$id]));
        $branchFilter->setLimit(new IntVal(1));
        return $this->getSimpleCarriers($branchFilter)->first();
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\SimpleCarrierIterator|\Traversable
     * @throws \Exception
     */
    public function getSimpleCarriersByCountry($country)
    {
        $branchFilter = new BranchFilter();

        $sample = new SimpleCarrierSample();
        $sample->setCountry($country);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }

    public function getHomeDeliveryCarriers(BranchFilter $branchFilter = null)
    {
        $branchFilter = new BranchFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(false);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }

    public function getPickupPointCarriers(BranchFilter $branchFilter = null)
    {
        $branchFilter = new BranchFilter();

        $sample = new SimpleCarrierSample();
        $sample->setPickupPoints(true);
        $branchFilter->setSimpleCarrierSample($sample);

        return $this->getSimpleCarriers($branchFilter);
    }
}
