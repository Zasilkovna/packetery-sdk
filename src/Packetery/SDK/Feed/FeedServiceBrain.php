<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Decimal;
use Packetery\SDK\Duration;
use Packetery\SDK\DurationUnit;
use Packetery\SDK\IStorage;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\Arrays;
use Packetery\Utils\Json;

class FeedServiceBrain
{
    /** @var \Packetery\SDK\Cache */
    private $cache;

    /** @var \Packetery\SDK\Client */
    private $client;

    /**
     * ApiFeedService constructor.
     *
     * @param \Packetery\SDK\Client $client
     */
    public function __construct(\Packetery\SDK\Client $client, IStorage $storage)
    {
        $this->client = $client;
        $this->cache = new Cache($storage);
    }

    private function createHDBranchFeedKey(BranchFilter $branchFilter = null)
    {
        $key = new StringVal('homeDeliveryBranchFeed');
        return $key->append($branchFilter->createApiHash());
    }

    private function createHDBranchFeedDuration()
    {
        return new Duration(Decimal::parse(3600), new DurationUnit(DurationUnit::SECOND));
    }

    public function isHDBranchFeedCached(BranchFilter $branchFilter = null)
    {
        $key = $this->createHDBranchFeedKey($branchFilter);
        return $this->cache->exists($key);
    }

    public function isHDBranchFeedExpired(BranchFilter $branchFilter = null)
    {
        $key = $this->createHDBranchFeedKey($branchFilter);
        return $this->cache->isExpired($key, $this->createHDBranchFeedDuration());
    }

    public function getHDBranchExport(BranchFilter $branchFilter = null)
    {
        $key = $this->createHDBranchFeedKey($branchFilter);
        $duration = $this->createHDBranchFeedDuration();
        return $this->cache->load(
            $key,
            function () use ($branchFilter) {
                $callResult = $this->client->getHDBranches($branchFilter);
                return $callResult->getResponseBody();
            },
            [
                Cache::OPTION_EXPIRATION => $duration->toSeconds()
            ]
        );
    }

    public function getHDBranchExportDecoded(BranchFilter $branchFilter = null)
    {
        $responseBody = $this->getHDBranchExport($branchFilter);
        return $this->decodeJsonContent($responseBody);
    }

    /**
     * @return \Packetery\SDK\Feed\HDCarrier[]|\Generator
     */
    public function getHDCarrierGenerator()
    {
        $decoded = $this->getHDBranchExportDecoded();
        $carriers = Arrays::getValue($decoded, ['carriers', 'carrier'], []);
        foreach ($carriers as $key => $carrier) {
            yield $key => HDCarrier::createFromArray($carrier); // so it does not matter how many items there are
        }
    }

    /**
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal|null $jsonContent
     * @return array|null
     */
    private function decodeJsonContent(StringVal $jsonContent = null)
    {
        if ($jsonContent === null || $jsonContent->isEmpty()) {
            return null;
        }

        return Json::decode((string)$jsonContent, Json::FORCE_ARRAY) ?: null;
    }
}
