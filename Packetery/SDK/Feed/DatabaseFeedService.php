<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Config;
use Packetery\SDK\Database\Connection;
use Packetery\SDK\IStorage;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\StringCollection;

class DatabaseFeedService implements IFeedService
{
    /** @var \Packetery\SDK\Database\Connection */
    private $connection;

    /** @var \Packetery\SDK\Feed\FeedServiceBrain */
    private $feedServiceBrain;

    /** @var \Packetery\SDK\Config */
    private $config;

    public function __construct(Connection $connection, FeedServiceBrain $feedServiceBrain, Config $config)
    {
        $this->connection = $connection;
        $this->feedServiceBrain = $feedServiceBrain;
        $this->config = $config;
    }

    public function getHDCarriers(BranchFilter $branchFilter = null)
    {
        if ($this->feedServiceBrain->isHDBranchFeedExpired(null) || !$this->feedServiceBrain->isHDBranchFeedCached(null)) {
            $generator = $this->feedServiceBrain->getHDCarrierGenerator(); // going to start download
            foreach ($generator as $HDCarrier) {
                // todo make transactional

                $prefix = $this->config->getTablePrefix();
                $sql = "DELETE FROM `{$prefix}packetery_home_delivery_carriers` WHERE `carrier_id` = {$this->connection->escape($HDCarrier->getId())}";
                $this->connection->query($sql);

                // todo multiinsert?
                $sql = "
                    INSERT INTO `{$prefix}packetery_home_delivery_carriers` (`carrier_id`, `name`, `country`)
                    VALUES ({$this->connection->escape($HDCarrier->getId())}, {$this->connection->escape($HDCarrier->getName())}, {$this->connection->escape($HDCarrier->getCountry())})
                ";
                $this->connection->query($sql);
            }
        }

        $conditions = ['1'];

        if ($branchFilter->getIds() && $branchFilter->getIds()->isEmpty()) {
            $imploded = implode(',', $branchFilter->getIds()->toValueArray());
            $conditions[] = "carrier_id IN ($imploded)";
        }

        if ($branchFilter->getCountry()) {
            $country = $this->connection->escape($branchFilter->getCountry());
            $conditions[] = "country = '$country'";
        }

        $limit = '';
        if ($branchFilter->getLimit() && $branchFilter->getLimit()->getValue() > 0) {
            $limitValue = $branchFilter->getLimit()->getValue();
            $limit = " LIMIT $limitValue";
        }

        $implodedWhere = implode(' AND ', $conditions);
        $sql = "SELECT phdc.* FROM {$prefix}packetery_home_delivery_carriers phdc WHERE $implodedWhere LIMIT $limit";
        $result = $this->connection->query($sql);

        $collection = new CarrierCollection();
        foreach ($result as $row) {
            $collection->add(HDCarrier::createFromArray((array)$row));
        }

        return $collection;
    }

    public function getHDCarrierById($id)
    {
        $branchFilter = new BranchFilter(StringCollection::createFromStrings([$id]));
        $collection = $this->getHDCarriers($branchFilter);
        return $collection->first();
    }

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
//        CREATE TABLE packetery_home_delivery_carriers (
//            carrier_id INT(11) NOT NULL,
//            name VARCHAR(255) NOT NULL,
//            country VARCHAR(255) NOT NULL
//        ) ENGINE=INNODB
//";
//
//        $this->connection->query(new StringVal($sql));
//    }
}
