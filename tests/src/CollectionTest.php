<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Feed\Carrier;
use Packetery\SDK\Feed\CarrierCollection;

class CollectionTest extends BaseTest
{

    public function testCarriers()
    {
        $data = $this->createSimpleCarrierCollection();
        $data = new CarrierCollection($data->toArray());

        $this->assertNotEmpty($data->last());
        $this->assertNotEmpty($data->first());

        $newAdded = new Carrier(99, 'added');
        $data->set(0, $newAdded);
        $this->assertEquals($data->first(), $newAdded);

        $data[0] = $newAdded;
        $this->assertEquals($data->first(), $newAdded);
        $this->assertEquals($data->get(0), $newAdded);

        $removedItem = $data->remove(1);
        $this->assertInstanceOf(Carrier::class, $removedItem);

        $removedItem = $data->remove(-1);
        $this->assertNull($removedItem);

        $item = $data->get(-1);
        $this->assertNull($item);
        $this->assertFalse($data->keyExists(-2));
        $this->assertTrue($data->keyExists(0));

        $this->assertEmpty($data[1]);
        $this->assertTrue(isset($data[2]));
        unset($data[3]);
        $this->assertFalse(isset($data[3]));
        $this->assertTrue($data->count() > 0);
        $this->assertEquals(json_encode($data), json_encode($data->toArray()));

        $collection = new CarrierCollection([]);
        $this->assertNull($collection->last());
        $this->assertException(InvalidArgumentException::class, function () use ($collection) {
                $collection->add(null);
        });
        $this->assertException(InvalidArgumentException::class, function () use ($collection) {
            $collection->add(
                [
                    'id' => 87,
                    'name' => 'name of carrier',
                ]
            );
        });
        $this->assertException(
            InvalidArgumentException::class,
            function () use ($collection) {
                new CarrierCollection(
                    [
                        [
                            'id' => 87,
                            'name' => 'name of carrier',
                        ]
                    ]
                );
            }
        );

        $count = 0;
        foreach ($collection as $carrier) {
            $count++;
        }
        $this->assertEquals($collection->count(), $count);

        $newLast = new Carrier(44, 'asdadas');
        $collection[] = $newLast;
        $this->assertEquals($collection->last(), $newLast);
    }
}
