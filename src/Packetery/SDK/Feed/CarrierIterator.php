<?php

namespace Packetery\SDK\Feed;

class CarrierIterator implements \IteratorAggregate
{
    /** @var \Iterator */
    private $iterable;

    public function __construct(\Iterator $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * @return \Generator|\Packetery\SDK\Feed\Carrier[]
     */
    public function getIterator()
    {
        return $this->getGenerator();
    }

    /**
     * @return \Generator
     */
    private function getGenerator()
    {
        foreach ($this->iterable as $key => $carrier) {
            yield $key => Carrier::createFromArray((array)$carrier);
        }
    }

    /**
     * @return \Packetery\SDK\Feed\CarrierCollection
     */
    public function createCollection()
    {
        return new CarrierCollection(iterator_to_array($this->iterable));
    }
}
