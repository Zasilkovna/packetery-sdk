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
    public function __construct(\Packetery\SDK\Client $client, IStorage $cacheStorage)
    {
        $this->client = $client;
        $this->cache = new Cache($cacheStorage);
    }

    private function createSimpleCarrierFeedKey(BranchFilter $branchFilter = null)
    {
        $key = new StringVal('homeDeliveryBranchFeed');
        return $key->append($branchFilter ? $branchFilter->createApiHash() : '');
    }

    private function createSimpleCarrierFeedDuration()
    {
        return new Duration(Decimal::parse(3600), new DurationUnit(DurationUnit::SECOND));
    }

    public function isSimpleCarrierFeedCached(BranchFilter $branchFilter = null)
    {
        $key = $this->createSimpleCarrierFeedKey($branchFilter);
        return $this->cache->exists($key);
    }

    public function isSimpleCarrierFeedExpired(BranchFilter $branchFilter = null)
    {
        $key = $this->createSimpleCarrierFeedKey($branchFilter);
        return $this->cache->isExpired($key, $this->createSimpleCarrierFeedDuration());
    }

    public function getSimpleCarrierExport(BranchFilter $branchFilter = null)
    {
        $key = $this->createSimpleCarrierFeedKey($branchFilter);
        $duration = $this->createSimpleCarrierFeedDuration();
        return $this->cache->load(
            $key,
            function () use ($branchFilter) {
                $callResult = $this->client->getSimpleCarriers($branchFilter);
                return $callResult->getResponseBody();
            },
            [
                Cache::OPTION_EXPIRATION => $duration->toSeconds()
            ]
        );
    }

    public function getSimpleCarrierExportDecoded(BranchFilter $branchFilter = null)
    {
        $responseBody = $this->getSimpleCarrierExport($branchFilter);
        return $this->decodeJsonContent($responseBody);
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrier[]|\Generator
     */
    public function getSimpleCarrierGenerator(BranchFilter $branchFilter = null)
    {
        $decoded = $this->getSimpleCarrierExportDecoded($branchFilter);
        $carriers = Arrays::getValue($decoded, ['carriers'], []);
        foreach ($carriers as $key => $carrier) {
            yield $key => SimpleCarrier::createFromFeedArray($carrier); // so it does not matter how many items there are
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
