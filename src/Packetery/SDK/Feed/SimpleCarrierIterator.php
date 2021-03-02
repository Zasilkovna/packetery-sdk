<?php

namespace Packetery\SDK\Feed;

class SimpleCarrierIterator implements \IteratorAggregate
{
    /** @var \Iterator */
    private $iterable;

    public function __construct(\Iterator $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * @return \Generator|\Packetery\SDK\Feed\SimpleCarrier[]
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
            if ($carrier instanceof SimpleCarrier) {
                $simpleCarrier = $carrier;
            } else {
                $carrierData = (array)$carrier;
                $simpleCarrier = SimpleCarrier::createFromDatabaseRow($carrierData);
            }

            yield $key => $simpleCarrier;
        }
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrier
     */
    public function first()
    {
        $iterable = $this->getGenerator();
        return $iterable->current();
    }
}
