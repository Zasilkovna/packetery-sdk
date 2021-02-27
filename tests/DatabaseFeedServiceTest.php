<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Mockery;
use Packetery\SDK\CallResult;
use Packetery\SDK\Client;
use Packetery\SDK\Config;
use Packetery\SDK\Container;
use Packetery\SDK\Database\Connection;
use Packetery\SDK\Database\IDriver;
use Packetery\SDK\Database\Result;
use Packetery\SDK\Feed\DatabaseFeedService;
use Packetery\SDK\Feed\DatabaseRepository;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Feed\HDCarrier;
use Packetery\SDK\Feed\HDCarrierIterator;
use Packetery\SDK\FileStorage;
use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\Json;

class DatabaseFeedServiceTest extends BaseTest
{

    public function testClassAutoload()
    {
        $container = require __DIR__ . '/../autoload.php';
        $container2 = require __DIR__ . '/../autoload.php';

        $this->assertTrue($container === $container2, 'Only one instance of container is allowed');

        $this->assertInstanceOf(
            Container::class,
            $container,
            'Container was not loaded properly'
        );

        $this->assertTrue(class_exists(Config::class), 'Config class was not loaded');
        $this->assertTrue(class_exists(DatabaseFeedService::class), 'DatabaseFeedService class was not loaded');
    }

    public function testHDCarrierIterator()
    {
        $data = [
            [
                'id' => 13,
                'name' => 'česká pošta',
                'country' => 'cz',
            ]
        ];

        $iterator = new HDCarrierIterator(
            new \ArrayIterator($data)
        );

        $array = iterator_to_array($iterator);
        $this->assertTrue(!empty($array), 'iterator_to_array fails to convert iterator array');

        $carrier = $iterator->first();
        $this->assertInstanceOf(HDCarrier::class, $carrier, 'carrier must be HDCarrier');

        $this->assertTrue(!empty($iterator), 'iterator must not be empty');

        $count = 0;
        foreach ($iterator as $car) {
            $count++;
            $this->assertInstanceOf(HDCarrier::class, $car, 'carrier must be HDCarrier');
        }

        $this->assertEquals(1, $count, 'iterator pointer must not move when calling first()');
    }

    /**
     * @depends testClassAutoload
     */
    public function testService()
    {
        $connection = Mockery::mock(Connection::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('getHDBranches')->andReturn(
            new CallResult(
                new BoolVal(true),
                StringVal::create(
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

        $fileStorage = new FileStorage(StringVal::create(__DIR__ . '/temp')); // todo test Cache

        $brain = new FeedServiceBrain($client, $fileStorage);

        $resultDriver = Mockery::mock(IDriver::class);
        $resultDriver->shouldReceive('getIterator')->andReturn(
            new \ArrayIterator(
                [
                    [
                        'id' => 13,
                        'name' => 'česká pošta',
                        'country' => 'cz',
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
        $repository->shouldReceive('findCarrier')->andReturn(new Result($resultDriver));
        $repository->shouldReceive('findHDCarrier')->andReturn(new Result($resultDriver));
        $repository->shouldReceive('insertHDCarrier')->andReturn(null);
        $repository->shouldReceive('updateHDCarrier')->times(0)->andReturn(null);

        $service = new DatabaseFeedService($connection, $brain, $repository);
        $carrierIterator = $service->getHDCarriersByCountry('cz');
        $carrier = $carrierIterator->first();

        $this->assertInstanceOf(HDCarrier::class, $carrier, 'carrier is not HDCarrier');
        $this->assertEquals('13', $carrier->getId()->getValue());
    }
}
