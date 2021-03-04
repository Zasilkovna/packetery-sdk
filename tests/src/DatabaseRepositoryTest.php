<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Feed\CarrierFilter;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierSample;

class DatabaseRepositoryTest extends BaseTest
{

    protected function setUp()
    {
        parent::setUp();
        $this->container->getConnection()->query("TRUNCATE TABLE `{$this->config->getTablePrefix()}packetery_carriers`");
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->container->getConnection()->query("TRUNCATE TABLE `{$this->config->getTablePrefix()}packetery_carriers`");
    }

    public function testConditions()
    {
        $container = $this->createContainer();
        $container->getConnection()->query("TRUNCATE TABLE `{$this->config->getTablePrefix()}packetery_carriers`");
        $service = $container->getDatabaseRepository();

        $service->insertCarrier(
            SimpleCarrier::createFromDatabaseRow(
                [
                    'carrier_id' => 13,
                    'name' => 'CZ POST HD',
                    'pickupPoints' => 0,
                    'apiAllowed' => 1,
                    'customsDeclarations' => 1,
                    'requiresEmail' => 1,
                    'requiresPhone' => 1,
                    'requiresSize' => 1,
                    'separateHouseNumber' => 1,
                    'disallowsCod' => 1,
                    'country' => 'cz',
                    'currency' => 'CZK',
                    'maxWeight' => '15',
                    'labelRouting' => 'A--0--000',
                    'labelName' => 'Carrier 1',
                    'in_feed' => 1,
                ]
            )
        );

        $filter = new CarrierFilter();
        $filter->setIds(['13']);
        $filter->setExcludedIds(['13']);
        $filter->setLimit(12);

        $sample = new SimpleCarrierSample();
        $sample->setInFeed(true);
        $sample->setCountry('sk');
        $sample->setPickupPoints(false);

        $filter->setSimpleCarrierSample($sample);

        $result = $service->findCarriers($filter);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());

        $filter = new CarrierFilter();
        $filter->buildSample('cz', false, true);
        $result = $service->findCarriers($filter);
        $this->assertEquals(1, $result->count(), 'czpost not found');

        $filter = new CarrierFilter();
        $filter->buildSample('cz', false, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(1, $result->count(), 'czpost not found');

        $filter = new CarrierFilter();
        $filter->buildSample('cz', true, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(0, $result->count(), 'czpost found');

        $filter = new CarrierFilter();
        $filter->buildSample('de', true, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(0, $result->count(), 'czpost found');

        $filter = new CarrierFilter();
        $filter->setIds([13]);
        $filter->buildSample('cz', false, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(1, $result->count(), 'czpost not found');

        $filter = new CarrierFilter();
        $filter->setIds([13]);
        $filter->setLimit(1);
        $filter->buildSample('cz', false, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(1, $result->count(), 'czpost not found');

        $filter = new CarrierFilter();
        $filter->setIds([13]);
        $filter->setLimit(0);
        $filter->buildSample('cz', false, null);
        $result = $service->findCarriers($filter);
        $this->assertEquals(0, $result->count(), 'czpost found');

        $filter = new CarrierFilter();
        $filter->setLimit(-1);
        $result = $service->findCarriers($filter);
        $this->assertEquals(0, $result->count(), 'czpost found');

        $service->insertCarrier(
            SimpleCarrier::createFromDatabaseRow(
                [
                    'carrier_id' => '16',
                    'name' => 'SK POST HD',
                    'pickupPoints' => 0,
                    'apiAllowed' => 1,
                    'customsDeclarations' => 1,
                    'requiresEmail' => 1,
                    'requiresPhone' => 1,
                    'requiresSize' => 1,
                    'separateHouseNumber' => 1,
                    'disallowsCod' => 1,
                    'country' => 'sk',
                    'currency' => 'EUR',
                    'maxWeight' => '10',
                    'labelRouting' => 'A--0--000',
                    'labelName' => 'Carrier 1',
                    'in_feed' => 1,
                ]
            )
        );

        $filter = new CarrierFilter();
        $filter->setIds([13, 16]);
        $result = $service->findCarriers($filter);
        $this->assertEquals(2, $result->count());

        $count = 0;
        foreach ($result as $carrier) {
            $this->assertNotEmpty($carrier);
            $this->assertInstanceOf(SimpleCarrier::class, SimpleCarrier::createFromDatabaseRow($carrier));
            $this->assertTrue(in_array(SimpleCarrier::createFromDatabaseRow($carrier)->getId(), $filter->getIds()));
            $count++;
        }

        $this->assertEquals(2, $count, 'foreach fail');

        $count = 0;
        foreach ($result as $carrier) {
            $this->assertNotEmpty($carrier);
            $this->assertInstanceOf(SimpleCarrier::class, SimpleCarrier::createFromDatabaseRow($carrier));
            $this->assertTrue(in_array(SimpleCarrier::createFromDatabaseRow($carrier)->getId(), $filter->getIds()));
            $count++;
        }

        $this->assertEquals(2, $count, 'foreach fail');

        $service->markAllInFeed(false);
        $result = $service->findCarriers();

        foreach ($result as $carrier) {
            $this->assertNotEmpty($carrier);
            $this->assertInstanceOf(SimpleCarrier::class, SimpleCarrier::createFromDatabaseRow($carrier));
            $this->assertFalse(SimpleCarrier::createFromDatabaseRow($carrier)->isInFeed());
        }

        $filter = new CarrierFilter();
        $filter->buildSample(null, null, false);
        $result = $service->findCarriers($filter);
        $this->assertCount(2, $result);

        $service->markAllInFeed(true);
        $result = $service->findCarriers();

        foreach ($result as $carrier) {
            $this->assertNotEmpty($carrier);
            $instance = SimpleCarrier::createFromDatabaseRow($carrier);
            $this->assertInstanceOf(SimpleCarrier::class, $instance);
            $this->assertTrue($instance->isInFeed());
        }

        $filter = new CarrierFilter();
        $filter->buildSample(null, null, true);
        $result = $service->findCarriers($filter);
        $this->assertCount(2, $result);

        $instance->setInFeed(false);
        $service->updateCarrier($instance);

        $filter = new CarrierFilter();
        $filter->buildSample(null, null, false);
        $result = $service->findCarriers($filter);
        $this->assertCount(1, $result);
    }
}
