<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Storage\IStorage;
use Packetery\Utils\Arrays;
use Packetery\Utils\Json;

class FeedServiceBrain
{
    /** @var \Packetery\SDK\Cache */
    private $cache;

    /** @var \Packetery\SDK\Client\Client */
    private $client;

    /**
     * ApiFeedService constructor.
     *
     * @param \Packetery\SDK\Client\Client $client
     */
    public function __construct(\Packetery\SDK\Client\Client $client, IStorage $cacheStorage)
    {
        $this->client = $client;
        $this->cache = new Cache($cacheStorage);
    }

    public function getSimpleCarrierExport()
    {
        $callResult = $this->client->getSimpleCarriers();
        return $callResult->getResponseBody();
    }

    public function getSimpleCarrierExportDecoded()
    {
        $responseBody = $this->getSimpleCarrierExport();
        return $this->decodeJsonContent($responseBody);
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrier[]|\Generator
     */
    public function getSimpleCarrierGenerator()
    {
        $decoded = $this->getSimpleCarrierExportDecoded();
        $carriers = Arrays::getValue($decoded, ['carriers'], []);
        foreach ($carriers as $key => $carrier) {
            yield $key => SimpleCarrier::createFromFeedArray($carrier); // so it does not matter how many items there are
        }
    }

    /**
     * @param string|null $jsonContent
     * @return array|null
     */
    private function decodeJsonContent($jsonContent = null)
    {
        if (empty($jsonContent)) {
            return null;
        }

        return Json::decode((string)$jsonContent, Json::FORCE_ARRAY) ?: null;
    }
}
