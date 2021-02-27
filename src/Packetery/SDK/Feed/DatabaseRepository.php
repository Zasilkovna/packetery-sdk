<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Config;

class DatabaseRepository
{
    /** @var \Packetery\SDK\Database\Connection */
    private $connection;

    /** @var string */
    private $dbPrefix;

    public function __construct(\Packetery\SDK\Database\Connection $connection, Config $config)
    {
        $this->connection = $connection;
        $this->dbPrefix = $config->getTablePrefix();
    }

    /**
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\Database\Result
     */
    public function findCarrier(BranchFilter $branchFilter = null)
    {
        if ($branchFilter->getForHomeDelivery() === true) {
            $conditions = ['phdc.is_home_delivery=1'];
        }

        if ($branchFilter->getIds() && $branchFilter->getIds()->isEmpty()) {
            $idCollection = $this->connection->escapeStringCollection($branchFilter->getIds());
            $imploded = $idCollection->implode(',');
            $conditions[] = "phdc.carrier_id IN ($imploded)";
        }

        if ($branchFilter->getCountry()) {
            $country = $this->connection->escapeText($branchFilter->getCountry());
            $conditions[] = "phdc.country = $country";
        }

        $limit = '';
        if ($branchFilter->getLimit() && $branchFilter->getLimit()->getValue() > 0) {
            $limitValue = $branchFilter->getLimit()->getValue();
            $limit = " LIMIT $limitValue";
        }

        $implodedWhere = implode(' AND ', $conditions);
        $sql = "SELECT phdc.* FROM `{$this->dbPrefix}packetery_carriers` phdc WHERE $implodedWhere $limit";
        return $this->connection->query($sql);
    }

    /**
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\Database\Result
     */
    public function findHDCarrier(BranchFilter $branchFilter = null)
    {
        return $this->findCarrier($branchFilter->setForHomeDelivery(true));
    }

    public function insertHDCarrier(HDCarrier $HDCarrier)
    {
        $sql = "
            INSERT INTO `{$this->dbPrefix}packetery_carriers` (`carrier_id`, `name`, `country`, `is_home_delivery`)
            VALUES ({$this->connection->escapeText($HDCarrier->getId())}, {$this->connection->escapeText($HDCarrier->getName())}, {$this->connection->escapeText($HDCarrier->getCountry())}, 1)
        ";

        $this->connection->query($sql);
    }

    public function updateHDCarrier(HDCarrier $HDCarrier)
    {
        $sql = "
            UPDATE `{$this->dbPrefix}packetery_carriers` 
            SET 
                `carrier_id`={$this->connection->escapeText($HDCarrier->getId())}, 
                `name`={$this->connection->escapeText($HDCarrier->getName())}, 
                `country`={$this->connection->escapeText($HDCarrier->getCountry())}, 
                `is_home_delivery`=1
            WHERE `carrier_id`={$HDCarrier->getId()}
        ";
        $this->connection->query($sql);
    }
}
