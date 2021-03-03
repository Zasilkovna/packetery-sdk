<?php

namespace Packetery\SDK;

use mysqli;
use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Client\Client;
use Packetery\SDK\Database\Connection;
use Packetery\SDK\Database\IDriver;
use Packetery\SDK\Database\MysqliDriver;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\DatabaseFeedService;
use Packetery\SDK\Feed\DatabaseRepository;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Storage\FileStorage;
use Packetery\SDK\Storage\MemoryStorage;
use PDO;

class Container
{
    /** @var \Packetery\SDK\Config */
    private $config;

    /** @var \Packetery\SDK\Cache */
    private $cache;

    /** @var IDriver */
    private $userDriver;

    public function __construct(Config $config, IDriver $userDriver)
    {
        $this->config = $config;
        $this->userDriver = $userDriver;
        $this->cache = new Cache(new MemoryStorage());
    }

    public static function create(array $configuration, $randomDriver = null)
    {
        $driver = null;
        $config = new Config($configuration);

        if ($randomDriver !== null) {
            if ($randomDriver instanceof mysqli) {
                throw new InvalidArgumentException('\mysqli wrapper not implemented');
            } elseif ($randomDriver instanceof PDO) {
                throw new InvalidArgumentException('\PDO wrapper not implemented');
            } else {
                throw new InvalidArgumentException('wrapper not implemented for given driver');
            }
        }

        if ($driver === null) {
            $driver = new MysqliDriver();
        }

        return new self($config, $driver);
    }

    /**
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                return new Connection($this->config, $this->userDriver);
            }
        );
    }

    /**
     * @return DatabaseRepository|null
     */
    public function getDatabaseRepository()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                return new DatabaseRepository($this->getConnection(), $this->config);
            }
        );
    }

    /**
     * @return DatabaseFeedService|null
     */
    public function getDatabaseFeedService()
    {
        return $this->cache->load(
            __METHOD__,
            function () {
                return new DatabaseFeedService(
                    $this->getConnection(),
                    $this->getFeedServiceBrain(),
                    $this->getDatabaseRepository()
                );
            }
        );
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
                    new FileStorage(Cache::createCacheFolder($dir))
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
                return new Client(($this->config->getApiBaseUrl()), ('v4'), ($this->config->getApiKey()));
            }
        );
    }
}
