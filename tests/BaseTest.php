<?php

namespace Packetery\Tests;

use Packetery\SDK\Cache;
use Packetery\SDK\Config;
use Packetery\SDK\Container;
use Packetery\SDK\Decimal;
use Packetery\SDK\Duration;
use Packetery\SDK\DurationUnit;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierCollection;
use Packetery\SDK\FileStorage;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\FS;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /** @var \Packetery\SDK\Config */
    protected $config;

    protected function setUp()
    {
        parent::setUp();
        $this->config = new Config(require __DIR__ . '/config.php');
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
        return new FileStorage(Cache::createCacheFolder(StringVal::create(__DIR__ . '/temp')));
    }

    protected function createDuration($decimal)
    {
        return new Duration(Decimal::parse($decimal), new DurationUnit(DurationUnit::SECOND));
    }

    protected function createContainer()
    {
        return new Container($this->config);
    }

    protected function createSimpleCarrierCollection()
    {
        $collection = new SimpleCarrierCollection();

        $carrier = new SimpleCarrier(StringVal::parse('13'), StringVal::parse('CZ POST HD'));
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(StringVal::parse('14'), StringVal::parse('CZ DPD HD'));
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(StringVal::parse('15'), StringVal::parse('DE HERMES HD'));
        $carrier->setCountry('de');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(StringVal::parse('16'), StringVal::parse('DE HERMES PP'));
        $carrier->setCountry('de');
        $carrier->setPickupPoints(true);
        $collection->add($carrier);

        $carrier = new SimpleCarrier(StringVal::parse('17'), StringVal::parse('SK POST HD'));
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
        } catch (\Exception $exception) {}

        $this->assertInstanceOf($exceptionClass, $exception, $message);
    }
}
