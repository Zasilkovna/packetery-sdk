<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\StringCollection;

class BranchFilter
{
    /** @var StringCollection|null */
    private $ids;

    /** @var IntVal|null */
    private $limit;

    /** @var bool|null */
    private $forHomeDelivery;

    /** @var \Packetery\SDK\Feed\SimpleCarrierSample|null */
    private $simpleCarrierSample;

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrierSample|null
     */
    public function getSimpleCarrierSample()
    {
        return $this->simpleCarrierSample;
    }

    public function setSimpleCarrierSample(SimpleCarrierSample $simpleCarrierSample = null)
    {
        $this->simpleCarrierSample = $simpleCarrierSample;
    }

    /**
     * @return bool
     */
    public function getForHomeDelivery()
    {
        return $this->forHomeDelivery;
    }

    public function setForHomeDelivery($forHomeDelivery = null)
    {
        $this->forHomeDelivery = $forHomeDelivery;
    }

    /**
     * @return \Packetery\SDK\PrimitiveTypeWrapper\IntVal
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit(IntVal $limit = null)
    {
        $this->limit = $limit;
    }

    /**
     * @return StringCollection|null
     */
    public function getIds()
    {
        return $this->ids;
    }

    public function setIds(StringCollection $ids = null)
    {
        $this->ids = $ids;
    }

    /** Returns API compatible assoc array for query building
     * @return array
     */
    public function toApiArray()
    {
        $result = [];

        if ($this->forHomeDelivery) {
            $result['address-delivery'] = 1;
        }

        return $result;
    }

    public function createApiHash()
    {
        return new StringVal(md5(serialize($this->toApiArray())));
    }
}
