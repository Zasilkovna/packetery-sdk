<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\StringCollection;

class CarrierFilter
{
    /** @var StringCollection|null */
    private $ids;

    /** @var StringCollection|null */
    private $excludedIds;

    /** @var array */
    private $apiParams = [];

    /** @var IntVal|null */
    private $limit;

    /** @var \Packetery\SDK\Feed\SimpleCarrierSample|null */
    private $simpleCarrierSample;

    /**
     * @return \Packetery\SDK\StringCollection|null
     */
    public function getExcludedIds()
    {
        return $this->excludedIds;
    }

    public function setExcludedIds(StringCollection $excludedIds = null)
    {
        $this->excludedIds = $excludedIds;
    }

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
     * @return \Packetery\SDK\PrimitiveTypeWrapper\IntVal
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit = null)
    {
        $this->limit = IntVal::parse($limit);
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
    public function getApiParams()
    {
        return $this->apiParams;
    }

    public function setApiParams(array $apiParams)
    {
        $this->apiParams = $apiParams;
    }

    public function createApiHash()
    {
        return new StringVal(md5(serialize($this->getApiParams())));
    }
}
