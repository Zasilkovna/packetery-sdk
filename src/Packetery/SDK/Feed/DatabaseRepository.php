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
        $andConditions = ['1'];

        if ($branchFilter) {
            if (!empty($branchFilter->getIds())) {
                $idCollection = $this->connection->escapeStringCollection($branchFilter->getIds());
                $imploded = implode(',', $idCollection);
                $andConditions[] = "phdc.carrier_id IN ($imploded)";
            }

            if (!empty($branchFilter->getExcludedIds())) {
                $idCollection = $this->connection->escapeStringCollection($branchFilter->getExcludedIds());
                $imploded = implode(',', $idCollection);
                $andConditions[] = "phdc.carrier_id NOT IN ($imploded)";
            }

            $simpleCarrierSample = $branchFilter->getSimpleCarrierSample();

            if ($simpleCarrierSample) {
                if ($simpleCarrierSample->getCountry()) {
                    $country = $this->connection->escapeText($simpleCarrierSample->getCountry());
                    $andConditions[] = "phdc.country = $country";
                }

                if ($simpleCarrierSample->isPickupPoints() === false) {
                    $andConditions[] = 'phdc.pickupPoints = 0';
                } else if ($simpleCarrierSample->isPickupPoints() === true) {
                    $andConditions[] = 'phdc.pickupPoints = 1';
                }

                if ($simpleCarrierSample->isInFeed() === true) {
                    $andConditions[] = 'phdc.in_feed = 1';
                } else if ($simpleCarrierSample->isInFeed() === false) {
                    $andConditions[] = 'phdc.in_feed = 0';
                }
            }

            if ($branchFilter->getLimit() !== null) {
                if ($branchFilter->getLimit() > 0) {
                    $limitValue = $branchFilter->getLimit();
                    $limit = " LIMIT $limitValue";
                } else {
                    $andConditions[] = '0';
                }
            }

        }

        $implodedWhere = implode(' AND ', $andConditions);
        $sql = "SELECT phdc.* FROM `{$this->dbPrefix}packetery_carriers` phdc WHERE $implodedWhere $limit";
        return $this->connection->query($sql);
    }

    public function insertCarrier(SimpleCarrier $simpleCarrier)
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
                                                   `labelName`,
                                                   `in_feed`
                                                   )
            VALUES (
                    {$this->connection->escapeText($simpleCarrier->getId())}, 
                    {$this->connection->escapeText($simpleCarrier->getName())}, 
                    
                    {$this->connection->escapeBool($simpleCarrier->isPickupPoints())}, 
                    {$this->connection->escapeBool($simpleCarrier->isSeparateHouseNumber())}, 
                    {$this->connection->escapeBool($simpleCarrier->isCustomsDeclarations())}, 
                    {$this->connection->escapeBool($simpleCarrier->isDisallowsCod())}, 
                    {$this->connection->escapeBool($simpleCarrier->isRequiresPhone())}, 
                    {$this->connection->escapeBool($simpleCarrier->isRequiresEmail())}, 
                    {$this->connection->escapeBool($simpleCarrier->isRequiresSize())}, 
                    {$this->connection->escapeBool($simpleCarrier->isApiAllowed())}, 
                    
                    {$this->connection->escapeText($simpleCarrier->getCountry())},
                    {$this->connection->escapeText($simpleCarrier->getCurrency())},
                    {$this->connection->escapeText($simpleCarrier->getMaxWeight())},
                    {$this->connection->escapeText($simpleCarrier->getLabelRouting())},
                    {$this->connection->escapeText($simpleCarrier->getLabelName())},
                    {$this->connection->escapeBool($simpleCarrier->isInFeed())}
                    )
        ";

        $this->connection->query($sql);
    }

    public function updateCarrier(SimpleCarrier $SimpleCarrier)
    {
        $sql = "
            UPDATE `{$this->dbPrefix}packetery_carriers` 
            SET 
                `name`={$this->connection->escapeText($SimpleCarrier->getName())}, 
                
                `pickupPoints`={$this->connection->escapeBool($SimpleCarrier->isPickupPoints())},
                `separateHouseNumber`={$this->connection->escapeBool($SimpleCarrier->isSeparateHouseNumber())},
                `customsDeclarations`={$this->connection->escapeBool($SimpleCarrier->isCustomsDeclarations())},
                `disallowsCod`={$this->connection->escapeBool($SimpleCarrier->isDisallowsCod())},
                `requiresPhone`={$this->connection->escapeBool($SimpleCarrier->isRequiresPhone())},
                `requiresEmail`={$this->connection->escapeBool($SimpleCarrier->isRequiresEmail())},
                `requiresSize`={$this->connection->escapeBool($SimpleCarrier->isRequiresSize())},
                `apiAllowed`={$this->connection->escapeBool($SimpleCarrier->isApiAllowed())},
                
                `country`={$this->connection->escapeText($SimpleCarrier->getCountry())},
                `currency`={$this->connection->escapeText($SimpleCarrier->getCurrency())},
                `maxWeight`={$this->connection->escapeText($SimpleCarrier->getMaxWeight())},
                `labelRouting`={$this->connection->escapeText($SimpleCarrier->getLabelRouting())},
                `labelName`={$this->connection->escapeText($SimpleCarrier->getLabelName())},
                `in_feed`={$this->connection->escapeBool($SimpleCarrier->isInFeed())}
            WHERE `carrier_id`={$this->connection->escapeText($SimpleCarrier->getId())}
        ";
        $this->connection->query($sql);
    }

    /** Only marks carrier that came from feed
     * @param bool $inFeed
     */
    public function markAllInFeed($inFeed)
    {
        $sql = "
            UPDATE `{$this->dbPrefix}packetery_carriers` 
            SET 
                `in_feed`={$this->connection->escapeBool($inFeed)}
            WHERE `in_feed` IS NOT NULL
        ";
        $this->connection->query($sql);
    }
}
