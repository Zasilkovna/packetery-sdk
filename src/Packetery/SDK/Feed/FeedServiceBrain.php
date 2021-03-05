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

    /** @var \Packetery\SDK\Config */
    private $config;

    /**
     * ApiFeedService constructor.
     *
     * @param \Packetery\SDK\Client\Client $client
     * @param \Packetery\SDK\Storage\IStorage $cacheStorage
     * @param \Packetery\SDK\Config $config
     */
    public function __construct(\Packetery\SDK\Client\Client $client, IStorage $cacheStorage, \Packetery\SDK\Config $config)
    {
        $this->client = $client;
        $this->cache = new Cache($cacheStorage);
        $this->config = $config;
    }

    /**
     * @return string|null
     * @throws \Packetery\SDK\Client\ClientException
     */
    public function getSimpleCarrierExport()
    {
        $callResult = $this->client->getSimpleCarriers();
        return $callResult ?: null;
    }

    /**
     * @return array|null
     */
    public function getSimpleCarrierExportDecoded()
    {
        $responseBody = null;
        try {
            // in case endpoint is unavailable
            $responseBody = $this->cache->load(
                'feedCache',
                function () {
                    return $this->getSimpleCarrierExport();
                },
                $this->config->getFeedCacheExpirationSeconds()
            );

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
