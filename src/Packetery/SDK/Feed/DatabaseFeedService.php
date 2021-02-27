<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Config;
use Packetery\SDK\Database\Connection;
use Packetery\SDK\IStorage;
use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\StringCollection;

class DatabaseFeedService implements IFeedService
{
    /** @var \Packetery\SDK\Database\Connection */
    private $connection;

    /** @var \Packetery\SDK\Feed\DatabaseRepository */
    private $repository;

    /** @var \Packetery\SDK\Feed\FeedServiceBrain */
    private $feedServiceBrain;

    public function __construct(Connection $connection, FeedServiceBrain $feedServiceBrain, DatabaseRepository $databaseRepository)
    {
        $this->connection = $connection;
        $this->feedServiceBrain = $feedServiceBrain;
        $this->repository = $databaseRepository;
    }

    private function isUpdateNeeded()
    {
        return !$this->feedServiceBrain->isHDBranchFeedCached(null) || $this->feedServiceBrain->isHDBranchFeedExpired(null);
    }

    private function updateData()
    {
        $generator = $this->feedServiceBrain->getHDCarrierGenerator(); // going to start new download if isUpdateNeeded()
        foreach ($generator as $HDCarrier) {
            $this->connection->transactional(
                function () use ($HDCarrier) {
                    $filter = new BranchFilter(StringCollection::createFromStrings([$HDCarrier->getId()->getValue()]));
                    $result = $this->repository->findHDCarrier($filter);

                    // todo multi insert?
                    if (!$result->isEmpty()) {
                        $this->repository->updateHDCarrier($HDCarrier);
                    } else {
                        $this->repository->insertHDCarrier($HDCarrier);
                    }
                }
            );
        }
    }

    /**
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\Feed\HDCarrierIterator
     * @throws \Exception
     */
    public function getHDCarriers(BranchFilter $branchFilter = null)
    {
        if ($this->isUpdateNeeded()) {
            $this->updateData();
        }

        $result = $this->repository->findHDCarrier($branchFilter);
        return new HDCarrierIterator(new \IteratorIterator($result->getIterator()));
    }

    /**
     * @param string $id
     * @return \Packetery\SDK\Feed\HDCarrier
     */
    public function getHDCarrierById($id)
    {
        $branchFilter = new BranchFilter(StringCollection::createFromStrings([$id]));
        $branchFilter = $branchFilter->setLimit(new IntVal(1));
        return $this->getHDCarriers($branchFilter)->first();
    }

    /**
     * @param string $country
     * @return \Packetery\SDK\Feed\HDCarrierIterator|\Traversable
     * @throws \Exception
     */
    public function getHDCarriersByCountry($country)
    {
        $branchFilter = new BranchFilter();
        $branchFilter = $branchFilter->setCountry(StringVal::create($country));
        return $this->getHDCarriers($branchFilter);
    }

//    private function checkSchema()
//    {
//        // todo fix and move to docs
//        $sql = /** @lang MariaDB */
//            "
//        CREATE TABLE packetery_carriers (
//            carrier_id INT(11) NOT NULL,
//            name VARCHAR(255) NOT NULL,
//            country VARCHAR(255) NOT NULL,
//            is_home_delivery smallint(1) NOT NULL
//        ) ENGINE=INNODB
//";
//
//        $this->connection->query(new StringVal($sql));
//    }
}
