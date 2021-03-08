<?php

namespace Packetery\Tests;

use Exception;
use Packetery\SDK\Config;
use Packetery\SDK\Container;
use Packetery\SDK\Feed\Carrier;
use Packetery\SDK\Feed\CarrierCollection;
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
        FS::removeFiles($this->config->getTempFolder() . '/*');
        @rmdir($this->config->getTempFolder() . '/cache');
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

    /**
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    protected function callPrivateMethod($object, $method, $arguments = [])
    {
        $rc = new \ReflectionClass($object);
        $method = $rc->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $arguments);
    }

    /**
     * @param string $name
     * @return \Packetery\SDK\Storage\FileStorage
     */
    protected function createCacheFileStorage($name = 'default')
    {
        return FileStorage::createCacheFileStorage(__DIR__ . '/temp', $name);
    }

    protected function createDuration($decimal)
    {
        return (float)$decimal;
    }

    protected function createContainer()
    {
        return new Container($this->config);
    }

    /**
     * @return \Packetery\SDK\Feed\CarrierCollection
     */
    protected function createSimpleCarrierCollection()
    {
        $collection = new CarrierCollection();

        $carrier = new Carrier('13', 'CZ POST HD');
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new Carrier('14', 'CZ DPD HD');
        $carrier->setCountry('cz');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new Carrier('15', 'DE HERMES HD');
        $carrier->setCountry('de');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = new Carrier('16', 'DE HERMES PP');
        $carrier->setCountry('de');
        $carrier->setPickupPoints(true);
        $collection->add($carrier);

        $carrier = new Carrier('17', 'SK POST HD');
        $carrier->setCountry('sk');
        $carrier->setPickupPoints(false);
        $collection->add($carrier);

        $carrier = Carrier::createFromFeedArray(
            [
                'id' => 18,
                'name' => 'HU TOF PP',
                'apiAllowed' => 1,
                'pickupPoints' => 'true',
                'customsDeclarations' => 'false',
                'requiresEmail' => 'true',
                'requiresPhone' => false,
                'requiresSize' => 1,
                'separateHouseNumber' => true,
                'disallowsCod' => 0,
                'country' => 'hu',
                'currency' => 'HUF',
                'labelName' => 'labelName',
                'labelRouting' => 'labelrouting--00-22',
                'maxWeight' => 10
            ]
        );

        $collection->add($carrier);

        return $collection;
    }

    /**
     * @param string $exceptionClass
     * @param callable $callback
     * @param string $message
     */
    protected function assertException($exceptionClass, callable $callback, $message = '')
    {
        $exception = null;

        try {
            call_user_func_array($callback, []);
        } catch (Exception $exception) {
        }

        if ($exception && $exceptionClass !== get_class($exception)) {
            $message .= $exception->getMessage();
        }

        $this->assertInstanceOf($exceptionClass, $exception, $message);
    }
}
