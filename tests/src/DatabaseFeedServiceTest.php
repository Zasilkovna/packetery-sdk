<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Mockery;
use Packetery\SDK\Client\CallResult;
use Packetery\SDK\Client\Client;
use Packetery\SDK\Config;
use Packetery\SDK\Container;
use Packetery\SDK\Database\Connection;
use Packetery\SDK\Database\IDriver;
use Packetery\SDK\Database\Result;
use Packetery\SDK\Feed\CarrierFilter;
use Packetery\SDK\Feed\DatabaseFeedService;
use Packetery\SDK\Feed\DatabaseRepository;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierIterator;
use Packetery\Utils\Json;

class DatabaseFeedServiceTest extends BaseTest
{

    public function testClassAutoload()
    {
        $this->assertTrue(class_exists(Container::class), 'Container class was not loaded');
        $this->assertTrue(class_exists(Config::class), 'Config class was not loaded');
        $this->assertTrue(class_exists(DatabaseFeedService::class), 'DatabaseFeedService class was not loaded');
    }

    public function testSimpleCarrierIterator()
    {
        $data = [
            [
                'id' => 13,
                'name' => 'česká pošta',
                'country' => 'cz',
            ]
        ];

        $iterator = new SimpleCarrierIterator(
            new \ArrayIterator($data)
        );

        $array = iterator_to_array($iterator);
        $this->assertTrue(!empty($array), 'iterator_to_array fails to convert iterator array');

        $carrier = $iterator->first();
        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier must be SimpleCarrier');

        $this->assertTrue(!empty($iterator), 'iterator must not be empty');

        $count = 0;
        foreach ($iterator as $car) {
            $count++;
            $this->assertInstanceOf(SimpleCarrier::class, $car, 'carrier must be SimpleCarrier');
        }

        $this->assertEquals(1, $count, 'iterator pointer must not move when calling first()');
    }

    /**
     * @depends testClassAutoload
     */
    public function testService()
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('transactional')->andReturn(null);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('getSimpleCarriers')->andReturn(
            new CallResult(
                true,
                (
                Json::encode(
                    [
                        'carriers' => [
                            [
                                'id' => 13,
                                'name' => 'česká pošta',
                            ]
                        ]
                    ]
                )
                )
            )
        );

        $fileStorage = $this->createCacheFileStorage();

        $brain = new FeedServiceBrain($client, $fileStorage);

        $resultDriver = Mockery::mock(IDriver::class);
        $resultDriver->shouldReceive('getIterator')->andReturn(
            new \ArrayIterator(
                [
                    [
                        'id' => 13,
                        'name' => 'česká pošta',
                        'pickupPoints' => 'false',
                        'apiAllowed' => true,
                        'customsDeclarations' => true,
                        'requiresEmail' => true,
                        'requiresPhone' => true,
                        'requiresSize' => true,
                        'separateHouseNumber' => false,
                        'disallowsCod' => true,
                        'country' => 'cz',
                        'currency' => 'CZK',
                        'maxWeight' => '15',
                        'labelRouting' => 'A--0--000',
                        'labelName' => 'Carrier 1',
                    ]
                ]
            )
        );

        $resultDriver->shouldReceive('fetch')->times(0);
        $resultDriver->shouldReceive('query')->times(0);
        $resultDriver->shouldReceive('begin')->andReturn(null);
        $resultDriver->shouldReceive('commit')->andReturn(null);
        $resultDriver->shouldReceive('rollback')->andReturn(null);

        $repository = Mockery::mock(DatabaseRepository::class);
        $repository->shouldReceive('findCarriers')->andReturn(new Result($resultDriver));
        $repository->shouldReceive('insertCarrier')->andReturn(null);
        $repository->shouldReceive('updateCarrier')->times(0)->andReturn(null);

        $service = new DatabaseFeedService($connection, $brain, $repository);
        $carrierIterator = $service->getSimpleCarriersByCountry('cz');
        $carrier = $carrierIterator->first();

        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');
        $this->assertEquals('13', $carrier->getId());
        $this->assertEquals('česká pošta', $carrier->getName());
        $this->assertEquals(false, $carrier->isPickupPoints());
        $this->assertEquals(true, $carrier->isApiAllowed());
        $this->assertEquals(true, $carrier->isDisallowsCod());
        $this->assertEquals(false, $carrier->isSeparateHouseNumber());
        $this->assertEquals(true, $carrier->isRequiresEmail());
        $this->assertEquals(true, $carrier->isRequiresPhone());
        $this->assertEquals(true, $carrier->isRequiresSize());
        $this->assertEquals('cz', $carrier->getCountry());
        $this->assertEquals('CZK', $carrier->getCurrency());
        $this->assertEquals('15', $carrier->getMaxWeight());
        $this->assertEquals('A--0--000', $carrier->getLabelRouting());
        $this->assertEquals('Carrier 1', $carrier->getLabelName());
    }

    /**
     * @depends testService
     */
    public function testAll()
    {
        $container = $this->createContainer();
        $service = $container->getDatabaseFeedService();
        $service->updateData();
        $carrierIterator = $service->getSimpleCarriersByCountry('cz');
        $carrier = $carrierIterator->first();

        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');

        $carrierIterator = $service->getHomeDeliveryCarriers();
        $carrier = $carrierIterator->first();

        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');

        $carrierIterator = $service->getHomeDeliveryCarriersByCountry('cz');
        $carrier = $carrierIterator->first();

        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');

        $carrierIterator = $service->getPickupPointCarriers();
        $carrier = $carrierIterator->first();

        $this->assertNotEmpty($carrier->getId(), 'carrier id is empty');
        $this->assertNotEmpty($carrier->getName(), 'carrier name is empty');
        $this->assertNotEmpty($carrier->getCountry(), 'carrier id is empty');
        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');

        $carrierById = $service->getSimpleCarrierById($carrier->getId());

        $this->assertNotEmpty($carrier->getId(), 'carrier id is empty');
        $this->assertNotEmpty($carrierById->getId(), '$carrierById carrier id is empty');
        $this->assertInstanceOf(SimpleCarrier::class, $carrierById, 'carrier is not SimpleCarrier');
        $this->assertEquals($carrier->getId(), $carrierById->getId(), '$carrierById id has different value');
        $this->assertEquals($carrier->getName(), $carrierById->getName(), '$carrierById name has different name');

        $filter = new CarrierFilter();
        $filter->setLimit(2);

        $carriersLimit2 = $service->getSimpleCarriers($filter);
        $data = iterator_to_array($carriersLimit2);
        $this->assertCount(2, $data, 'limit or iterator or feed db sync doesnt work');
        $this->assertCount(2, $data, 'limit or iterator or feed db sync doesnt work');

        $this->assertNotEquals($carrier1 = array_shift($data), $carrier2 = array_shift($data), 'data items are same');
        $this->assertNotEquals($carrier1->getId(), $carrier2->getId(), 'data items are same');
    }
}
