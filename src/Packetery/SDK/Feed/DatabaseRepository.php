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
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Database\Result
     */
    public function findCarriers(CarrierFilter $branchFilter = null)
    {
        $limit = '';
        $conditions = ['1'];

        if ($branchFilter) {
            if (!empty($branchFilter->getIds())) {
                $idCollection = $this->connection->escapeStringCollection($branchFilter->getIds());
                $imploded = implode(',', $idCollection);
                $conditions[] = "phdc.carrier_id IN ($imploded)";
            }

            if (!empty($branchFilter->getExcludedIds())) {
                $idCollection = $this->connection->escapeStringCollection($branchFilter->getExcludedIds());
                $imploded = implode(',', $idCollection);
                $conditions[] = "phdc.carrier_id NOT IN ($imploded)";
            }

            $simpleCarrierSample = $branchFilter->getSimpleCarrierSample();

            if ($simpleCarrierSample) {
                if ($simpleCarrierSample->getCountry()) {
                    $country = $this->connection->escapeText($simpleCarrierSample->getCountry());
                    $conditions[] = "phdc.country = $country";
                }

                if ($simpleCarrierSample->isPickupPoints() === false) {
                    $conditions[] = 'phdc.pickupPoints = 0';
                } else if ($simpleCarrierSample->isPickupPoints() === true) {
                    $conditions[] = 'phdc.pickupPoints = 1';
                }
            }

            if ($branchFilter->getLimit() && $branchFilter->getLimit() > 0) {
                $limitValue = $branchFilter->getLimit();
                $limit = " LIMIT $limitValue";
            }
        }

        $implodedWhere = implode(' AND ', $conditions);
        $sql = "SELECT phdc.* FROM `{$this->dbPrefix}packetery_carriers` phdc WHERE $implodedWhere $limit"; // todo fix cache memory issue about unexported branch
        return $this->connection->query($sql);
    }

    /**
     * @param \Packetery\SDK\Feed\CarrierFilter|null $branchFilter
     * @return \Packetery\SDK\Database\Result
     */
    public function findSimpleCarrier(CarrierFilter $branchFilter = null)
    {
        return $this->findCarriers($branchFilter);
    }

    public function insertCarrier(SimpleCarrier $SimpleCarrier)
    {
        $sql = "
            INSERT INTO `{$this->dbPrefix}packetery_carriers` (
                                                   `carrier_id`, 
                                                   `name`, 
                                                   
                                                   `pickupPoints`, 
                                                   `separateHouseNumber`, 
                                                   `customsDeclarations`, 
                                                   `disallowsCod`, 
                                                   `requiresPhone`, 
                                                   `requiresEmail`, 
                                                   `requiresSize`, 
                                                   `apiAllowed`, 
                                                   
                                                   `country`, 
                                                   `currency`, 
                                                   `maxWeight`, 
                                                   `labelRouting`, 
                                                   `labelName`
                                                   )
            VALUES (
                    {$this->connection->escapeText($SimpleCarrier->getId())}, 
                    {$this->connection->escapeText($SimpleCarrier->getName())}, 
                    
                    {$this->connection->escapeText($SimpleCarrier->isPickupPoints())}, 
                    {$this->connection->escapeText($SimpleCarrier->isSeparateHouseNumber())}, 
                    {$this->connection->escapeText($SimpleCarrier->isCustomsDeclarations())}, 
                    {$this->connection->escapeText($SimpleCarrier->isDisallowsCod())}, 
                    {$this->connection->escapeText($SimpleCarrier->isRequiresPhone())}, 
                    {$this->connection->escapeText($SimpleCarrier->isRequiresEmail())}, 
                    {$this->connection->escapeText($SimpleCarrier->isRequiresSize())}, 
                    {$this->connection->escapeText($SimpleCarrier->isApiAllowed())}, 
                    
                    {$this->connection->escapeText($SimpleCarrier->getCountry())},
                    {$this->connection->escapeText($SimpleCarrier->getCurrency())},
                    {$this->connection->escapeText($SimpleCarrier->getMaxWeight())},
                    {$this->connection->escapeText($SimpleCarrier->getLabelRouting())},
                    {$this->connection->escapeText($SimpleCarrier->getLabelName())}
                    )
        ";

        $this->connection->query($sql);
    }

    public function updateCarrier(SimpleCarrier $SimpleCarrier)
    {
        $sql = "
            UPDATE `{$this->dbPrefix}packetery_carriers` 
            SET 
                `carrier_id`={$this->connection->escapeText($SimpleCarrier->getId())}, 
                `name`={$this->connection->escapeText($SimpleCarrier->getName())}, 
                
                `pickupPoints`={$this->connection->escapeText($SimpleCarrier->isPickupPoints())},
                `separateHouseNumber`={$this->connection->escapeText($SimpleCarrier->isSeparateHouseNumber())},
                `customsDeclarations`={$this->connection->escapeText($SimpleCarrier->isCustomsDeclarations())},
                `disallowsCod`={$this->connection->escapeText($SimpleCarrier->isDisallowsCod())},
                `requiresPhone`={$this->connection->escapeText($SimpleCarrier->isRequiresPhone())},
                `requiresEmail`={$this->connection->escapeText($SimpleCarrier->isRequiresEmail())},
                `requiresSize`={$this->connection->escapeText($SimpleCarrier->isRequiresSize())},
                `apiAllowed`={$this->connection->escapeText($SimpleCarrier->isApiAllowed())},
                
                `country`={$this->connection->escapeText($SimpleCarrier->getCountry())},
                `currency`={$this->connection->escapeText($SimpleCarrier->getCurrency())},
                `maxWeight`={$this->connection->escapeText($SimpleCarrier->getMaxWeight())},
                `labelRouting`={$this->connection->escapeText($SimpleCarrier->getLabelRouting())},
                `labelName`={$this->connection->escapeText($SimpleCarrier->getLabelName())}
            WHERE `carrier_id`={$this->connection->escapeText($SimpleCarrier->getId())}
        ";
        $this->connection->query($sql);
    }
}
