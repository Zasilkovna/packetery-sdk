<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Cache;
use Packetery\SDK\Client\ClientException;
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

    /**
     * @return string|null
     * @throws \Packetery\SDK\Client\ClientException
     */
    public function getSimpleCarrierExport()
    {
        $callResult = $this->client->getSimpleCarriers();
        return $callResult->getResponseBody();
    }

    /**
     * @return array|null
     */
    public function getSimpleCarrierExportDecoded()
    {
        $responseBody = null;
        try {
            $responseBody = $this->cache->load('feedCache', function () {
                return $this->getSimpleCarrierExport();
            }, [
                Cache::OPTION_EXPIRATION => 60
            ]);

        } catch (ClientException $exception) {
        }

        return $responseBody ? $this->decodeJsonContent($responseBody) : null;
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrier[]|\Generator
     */
    public function getSimpleCarrierGenerator()
    {
        $decoded = $this->getSimpleCarrierExportDecoded();
        $carriers = Arrays::getValue($decoded ?: [], ['carriers'], []);
        foreach ($carriers as $key => $carrier) {
            $instance = SimpleCarrier::createFromFeedArray($carrier);
            $instance->setInFeed(true);
            yield $key => $instance; // so it does not matter how many items there are
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
