<?php

namespace Packetery\SDK\Feed;

class CarrierFilter
{
    /** @var array|null */
    private $ids;

    /** @var array|null */
    private $excludedIds;

    /** @var array */
    private $apiParams = [];

    /** @var int|null */
    private $limit;

    /** @var \Packetery\SDK\Feed\SimpleCarrierSample|null */
    private $simpleCarrierSample;

    /**
     * @return array|null
     */
    public function getExcludedIds()
    {
        return $this->excludedIds;
    }

    public function setExcludedIds(array $excludedIds = null)
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
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit = null)
    {
        $this->limit = $limit;
    }

    /**
     * @return array|null
     */
    public function getIds()
    {
        return $this->ids;
    }

    public function setIds(array $ids = null)
    {
        $this->ids = $ids;
    }

    /** Returns API compatible assoc array for query building
     *
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
        return (md5(serialize($this->getApiParams())));
    }
}
