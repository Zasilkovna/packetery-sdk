<?php

namespace Packetery\Tests;

use Exception;
use Packetery\SDK\Cache;
use Packetery\SDK\Config;
use Packetery\SDK\Container;
use Packetery\SDK\Database\MysqliDriver;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierCollection;
use Packetery\SDK\Storage\FileStorage;
use Packetery\Utils\FS;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /** @var \Packetery\SDK\Config */
    protected $config;

    /** @var Container */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->config = new Config(require __DIR__ . '/config.php');
        $this->container = $this->createContainer();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $files = FS::rglob(__DIR__ . '/temp/*', GLOB_NOSORT);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || is_dir($file)) {
                continue;
            }

            unlink($file);
        }
    }

    protected function createCacheFileStorage()
    {
        return new FileStorage(Cache::createCacheFolder((__DIR__ . '/temp')));
    }

    protected function createDuration($decimal)
    {
        return (float)$decimal;
    }

    protected function createContainer()
    {
        return new Container($this->config, new MysqliDriver());
    }

    protected function createSimpleCarrierCollection()
    {
        $collection = new SimpleCarrierCollection();

        $carrier = new SimpleCarrier(('13'), ('CZ POST HD'));
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(('14'), ('CZ DPD HD'));
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(('15'), ('DE HERMES HD'));
        $carrier->setCountry('de');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(('16'), ('DE HERMES PP'));
        $carrier->setCountry('de');
        $carrier->setPickupPoints(true);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(('17'), ('SK POST HD'));
        $carrier->setCountry('sk');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        return $collection;
    }

    protected function assertException($exceptionClass, callable $callback, $message)
    {
        $exception = null;

        try {
            call_user_func_array($callback, []);
        } catch (Exception $exception) {
        }

        $this->assertInstanceOf($exceptionClass, $exception, $message);
    }
}
