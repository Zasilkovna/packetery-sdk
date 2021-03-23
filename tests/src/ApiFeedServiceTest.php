<?php

namespace Packetery\Tests;

use Mockery;
use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\CarrierFilter;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Feed\Carrier;

require __DIR__ . '/../autoload.php';

class ApiFeedServiceTest extends BaseTest
{
    public function testService()
    {
        $testContainer = $this->createContainer();
        $brain = new FeedServiceBrain($testContainer->getClient(), $this->createCacheFileStorage(), $this->config);
        $service = new ApiFeedService($brain);

        $carriers = $service->getAddressDeliveryCarriersByCountry('cz');
        $this->assertNotEmpty($carriers, 'is empty');
        $this->assertTrue($carriers->count() > 0, 'count is 0');
        $carrier = $carriers->first();
        $this->assertInstanceOf(Carrier::class, $carrier);

        $ids = [];
        foreach ($carriers as $carrier) {
            $ids[] = (string)$carrier->getId();
            $this->assertNotEmpty((string)$carrier->getId(), 'id is empty');
            $this->assertEquals($carrier->getCountry(), 'cz');
            $this->assertEquals($carrier->isPickupPoints(), false);
        }

        $this->assertNotEmpty($ids);
        $uniqueIds = array_unique($ids);
        $this->assertCount(count($uniqueIds), $ids, 'collection contains duplicate carriers');

        $id = array_shift($ids);
        $carrier = $service->getCarrierById($id);
        $this->assertInstanceOf(Carrier::class, $carrier, 'carrier is not SimpleCarrier');
        $this->assertEquals((string)$carrier->getId(), $id, 'service returned incorrect carrier');

        $carrier = $service->getCarrierById('');
        $this->assertEquals(null, $carrier, 'service didnt retuned null when should');

        $carriers = $service->getCarriersByCountry('sk');
        $this->assertTrue($carriers->count() > 0, 'count is 0');

        $ids = [];
        foreach ($carriers->toArray() as $carrier) {
            $ids[] = (string)$carrier->getId();
            $this->assertNotEmpty((string)$carrier->getId(), 'id is empty');
            $this->assertEquals($carrier->getCountry(), 'sk');
        }

        $this->assertNotEmpty($ids);
        $uniqueIds = array_unique($ids);
        $this->assertCount(count($uniqueIds), $ids, 'collection contains duplicate carriers');

        $carriers = $service->getPickupPointCarriersByCountry('de');
        $this->assertTrue($carriers->count() > 0, 'count is 0');

        $ids = [];
        foreach ($carriers->toArray() as $carrier) {
            $ids[] = (string)$carrier->getId();
            $this->assertNotEmpty((string)$carrier->getId(), 'id is empty');
            $this->assertEquals($carrier->getCountry(), 'de');
        }

        $this->assertNotEmpty($ids);
        $uniqueIds = array_unique($ids);
        $this->assertCount(count($uniqueIds), $ids, 'collection contains duplicate carriers');
    }

    public function testFilter()
    {
        $generatorData = $this->createSimpleCarrierCollection();

        $this->assertFalse($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => 'false'], ['field']]));
        $this->assertFalse($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => 0], ['field']]));
        $this->assertFalse($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => false], ['field']]));
        $this->assertTrue($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => 'true'], ['field']]));
        $this->assertTrue($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => 1], ['field']]));
        $this->assertTrue($this->callPrivateMethod($generatorData->last(), 'parseBool', [['field' => true], ['field']]));

        $this->assertEquals(18, $generatorData->last()->getId());
        $this->assertEquals(10, $generatorData->last()->getMaxWeight());
        $this->assertEquals('HUF', $generatorData->last()->getCurrency());
        $this->assertEquals('HU TOF PP', $generatorData->last()->getName());
        $this->assertEquals('labelName', $generatorData->last()->getLabelName());
        $this->assertEquals('labelrouting--00-22', $generatorData->last()->getLabelRouting());
        $this->assertEquals('hu', $generatorData->last()->getCountry());
        $this->assertEquals(true, $generatorData->last()->isRequiresSize());
        $this->assertEquals(true, $generatorData->last()->isRequiresEmail());
        $this->assertEquals(false, $generatorData->last()->isRequiresPhone());
        $this->assertEquals(false, $generatorData->last()->isCustomsDeclarations());
        $this->assertEquals(true, $generatorData->last()->isPickupPoints());
        $this->assertEquals(false, $generatorData->last()->isDisallowsCod());
        $this->assertEquals(true, $generatorData->last()->isSeparateHouseNumber());
        $this->assertEquals(true, $generatorData->last()->isApiAllowed());

        /** @var ApiFeedService $service */
        $service = Mockery::mock(
            ApiFeedService::class,
            [
                Mockery::mock(FeedServiceBrain::class)->shouldReceive('getSimpleCarrierGenerator')->andReturn($generatorData->getIterator())->getMock()
            ]
        )->makePartial();

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));
        $result = $service->getCarriers($filter);
        $this->checkCollections($filter, $result);

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));
        $filter->setCountries(['cz']);
        $result = $service->getCarriers($filter);
        $this->checkCollections($filter, $result);

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));
        $filter->setCountries(['de']);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();
        $filter->setCountries(['de']);
        $result = $service->getCarriers($filter);
        $this->assertCount(2, $result);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result->count());

        $filter = new CarrierFilter();
        $filter->setCountries(['de']);
        $filter->setHasPickupPoints(true);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);

        $generatorData = $this->createSimpleCarrierCollection();

        $filter = new CarrierFilter();
        $filter->buildSample('cz', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('cz', true);
        $result = $service->getCarriers($filter);
        $this->assertCount(2, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('cz', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('cz', null);
        $result = $service->getCarriers($filter);
        $this->assertCount(2, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('sk', null);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('sk', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();
        $filter->buildSample('de', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);
        $this->assertEquals('de', $result->first()->getCountry());

        $filter = new CarrierFilter();
        $filter->buildSample('hu', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);
        $this->assertEquals('hu', $result->first()->getCountry());
        $this->assertEquals(18, $result->first()->getId());
        $this->assertEquals(false, $result->first()->isCustomsDeclarations());
        $this->assertEquals(true, $result->first()->isRequiresSize());
        $this->assertEquals(false, $result->first()->isDisallowsCod());

        $filter = new CarrierFilter();
        $filter->setLimit(0);
        $filter->buildSample(null, null);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();
        $filter->setLimit(3);
        $filter->buildSample(null, null);
        $result = $service->getCarriers($filter);
        $this->assertCount(3, $result);

        $filter = new CarrierFilter();
        $filter->setIds([18, 17, 12, 'adadadadadad']);
        $filter->buildSample('hu', false);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);

        $filter = new CarrierFilter();
        $filter->setRequiresCustomsDeclarations(false);
        $filter->buildSample('sk', null);
        $result = $service->getCarriers($filter);
        $this->assertCount(1, $result);

        $filter = new CarrierFilter();
        $filter->setRequiresCustomsDeclarations(true);
        $result = $service->getCarriers($filter);
        $this->assertCount(0, $result);
    }

    private function checkCollections($filter, $result)
    {
        $this->assertEquals(count($filter->getIds()), $result->count(), 'incorrect count');

        foreach ($result as $carrier) {
            $this->assertTrue(in_array($carrier->getId(), $filter->getIds()), 'incorrect content');
        }
    }
}
