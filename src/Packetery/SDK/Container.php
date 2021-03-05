<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Client\Client;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Storage\FileStorage;
use Packetery\SDK\Storage\MemoryStorage;

class Container
{
    /** @var \Packetery\SDK\Config */
    private $config;

    /** @var \Packetery\SDK\Cache */
    private $cache;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->cache = new Cache(new MemoryStorage());
    }

    public static function create(array $configuration)
    {
        $config = new Config($configuration);
        return new self($config);
    }

    /**
     * @return ApiFeedService|null
     */
    public function getApiFeedService()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                return new ApiFeedService(
                    $this->getFeedServiceBrain()
                );
            }
        );
    }

    /**
     * @return FeedServiceBrain|null
     */
    public function getFeedServiceBrain()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                $dir = ($this->config->getTempFolder());

                return new FeedServiceBrain(
                    $this->getClient(),
                    FileStorage::createCacheFileStorage($dir),
                    $this->config
                );
            }
        );
    }

    /**
     * @return Client|null
     */
    public function getClient()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                return new Client($this->config);
            }
        );
    }
}
