<?php

namespace Packetery\SDK\Feed;

class HDCarrierIterator implements \IteratorAggregate
{
    /** @var \Iterator */
    private $iterable;

    public function __construct(\Iterator $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * @return \Generator|\Packetery\SDK\Feed\HDCarrier[]
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
        foreach (new \IteratorIterator($this->iterable) as $key => $carrier) {
            yield $key => HDCarrier::createFromArray((array)$carrier);
        }
    }

    /**
     * @return \Packetery\SDK\Feed\HDCarrier
     */
    public function first()
    {
        $iterable = $this->getGenerator();
        return $iterable->current();
    }
}
