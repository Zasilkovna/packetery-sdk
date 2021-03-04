<?php

namespace Packetery\Tests;

use Mockery;
use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\CarrierFilter;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierSample;

require __DIR__ . '/../autoload.php';

class ApiFeedServiceTest extends BaseTest
{
    public function testService()
    {
        $testContainer = $this->createContainer();
        $brain = new FeedServiceBrain($testContainer->getClient(), $this->createCacheFileStorage());
        $service = new ApiFeedService($brain);

        $carriers = $service->getAddressDeliveryCarriersByCountry('cz');
        $this->assertNotEmpty($carriers, 'is empty');
        $this->assertTrue($carriers->count() > 0, 'count is 0');
        $carrier = $carriers->first();
        $this->assertInstanceOf(SimpleCarrier::class, $carrier);

        $ids = [];
        foreach ($carriers as $carrier) {
            $ids[] = (string)$carrier->getId();
            $this->assertNotEmpty((string)$carrier->getId(), 'id is empty');
            $this->assertEquals($carrier->getCountry(), 'cz');
            $this->assertEquals($carrier->isPickupPoints(), false);
        }

        $uniqueIds = array_unique($ids);
        $this->assertCount(count($uniqueIds), $ids, 'collection contains duplicate carriers');

        $id = array_shift($ids);
        $carrier = $service->getSimpleCarrierById($id);
        $this->assertInstanceOf(SimpleCarrier::class, $carrier, 'carrier is not SimpleCarrier');
        $this->assertEquals((string)$carrier->getId(), $id, 'service returned incorrect carrier');

        $carrier = $service->getSimpleCarrierById('');
        $this->assertEquals(null, $carrier, 'service didnt retuned null when should');
    }

    public function testFilter()
    {
        $generatorData = $this->createSimpleCarrierCollection();

        /** @var ApiFeedService $service */
        $service = Mockery::mock(
            ApiFeedService::class,
            [
                Mockery::mock(FeedServiceBrain::class)->shouldReceive('getSimpleCarrierGenerator')->andReturn($generatorData->getIterator())->getMock()
            ]
        )->makePartial();

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));
        $result = $service->getSimpleCarriers($filter);
        $this->checkCollections($filter, $result);

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));

        $sample = new SimpleCarrierSample();
        $sample->setCountry('cz');

        $filter->setSimpleCarrierSample($sample);
        $result = $service->getSimpleCarriers($filter);
        $this->checkCollections($filter, $result);

        $filter = new CarrierFilter();
        $filter->setIds((['13', '14']));

        $sample = new SimpleCarrierSample();
        $sample->setCountry('de');

        $filter->setSimpleCarrierSample($sample);
        $result = $service->getSimpleCarriers($filter);
        $this->assertCount(0, $result);

        $filter = new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setCountry('de');

        $filter->setSimpleCarrierSample($sample);
        $result = $service->getSimpleCarriers($filter);
        $this->assertCount(2, $result);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result->count());

        $filter = new CarrierFilter();

        $sample = new SimpleCarrierSample();
        $sample->setCountry('de');
        $sample->setPickupPoints(true);

        $filter->setSimpleCarrierSample($sample);
        $result = $service->getSimpleCarriers($filter);
        $this->assertCount(1, $result);
    }

    public function testDeniedSampleMethods()
    {
        $this->assertException(
            InvalidStateException::class,
            function () {
                $sample = new SimpleCarrierSample();
                $sample->setName('czpost');
            },
            'SimpleCarrierSample __call check not working'
        );
    }

    private function checkCollections($filter, $result)
    {
        $this->assertEquals(count($filter->getIds()), $result->count(), 'incorrect count');

        foreach ($result as $carrier) {
            $this->assertTrue(in_array($carrier->getId(), $filter->getIds()), 'incorrect content');
        }
    }
}
