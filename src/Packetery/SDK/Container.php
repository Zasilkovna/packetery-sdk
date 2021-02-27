<?php

namespace Packetery\SDK;

use Packetery\SDK\Database\Connection;
use Packetery\SDK\Database\MysqliDriver;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\DatabaseFeedService;
use Packetery\SDK\Feed\DatabaseRepository;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class Container
{
    /** @var \Packetery\SDK\Config */
    private $config;

    /**
     * Container constructor.
     *
     * @param array $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getConnection()
    {
        $driver = new MysqliDriver();
        $driver->connect($this->config->getConnection()); // todo does it throw eception?
        return new Connection($driver); // todo must connect after?
    }

    public function getDatabaseRepository()
    {
        return new DatabaseRepository($this->getConnection(), $this->config);
    }

    public function getDatabaseFeedService()
    {
        return new DatabaseFeedService(
            $this->getConnection(),
            $this->getFeedServiceBrain(),
            $this->getDatabaseRepository()
        );
    }

    public function getApiFeedService()
    {
        return new ApiFeedService(
            $this->getFeedServiceBrain()
        );
    }

    public function getFeedServiceBrain()
    {
        $dir = new StringVal($this->config->getTempFolder());

        return new FeedServiceBrain(
            $this->getClient(),
            new FileStorage(Cache::createCacheFolder($dir))
        );
    }

    public function getClient()
    {
        return new Client(StringVal::parse($this->config->getApiBaseUrl()), new StringVal('v4'), StringVal::parse($this->config->getApiKey()));
    }
}
