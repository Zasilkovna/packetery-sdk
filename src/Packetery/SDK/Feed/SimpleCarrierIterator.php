<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Database\IDriverResult;

class SimpleCarrierIterator implements \IteratorAggregate, \Countable
{
    /** @var IDriverResult */
    private $iterable;

    public function __construct(IDriverResult $iterable)
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
     * @return \Packetery\SDK\Feed\SimpleCarrier|null
     */
    public function first()
    {
        $iterable = $this->getGenerator();
        return $iterable ? $iterable->current() : null;
    }

    public function isEmpty()
    {
        return $this->iterable->count() == 0;
    }

    public function count()
    {
        return $this->iterable->count();
    }
}
