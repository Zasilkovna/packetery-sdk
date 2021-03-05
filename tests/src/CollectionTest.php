<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Feed\SimpleCarrier;
use Packetery\SDK\Feed\SimpleCarrierCollection;

class CollectionTest extends BaseTest
{

    public function testCarriers()
    {
        $data = $this->createSimpleCarrierCollection();
        $data = new SimpleCarrierCollection($data->toArray());

        $this->assertNotEmpty($data->last());
        $this->assertNotEmpty($data->first());

        $newAdded = new SimpleCarrier(99, 'added');
        $data->set(0, $newAdded);
        $this->assertEquals($data->first(), $newAdded);

        $data[0] = $newAdded;
        $this->assertEquals($data->first(), $newAdded);
        $this->assertEquals($data->get(0), $newAdded);

        $removedItem = $data->remove(1);
        $this->assertInstanceOf(SimpleCarrier::class, $removedItem);

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

        $collection = new SimpleCarrierCollection([]);
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
                new SimpleCarrierCollection(
                    [
                        [
                            'id' => 87,
                            'name' => 'name of carrier',
                        ]
                    ]
                );
            }
        );

        $newLast = new SimpleCarrier(44, 'asdadas');
        $collection[] = $newLast;
        $this->assertEquals($collection->last(), $newLast);
    }
}
