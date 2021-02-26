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

    /** @var StringVal|null */
    private $country;

    /** @var IntVal|null */
    private $limit;

    /**
     * BranchFilter constructor.
     *
     * @param \Packetery\SDK\StringCollection|null $ids
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal|null $country
     * @param \Packetery\SDK\PrimitiveTypeWrapper\BoolVal|null $forHomeDelivery
     * @param \Packetery\SDK\PrimitiveTypeWrapper\IntVal|null $limit
     */
    public function __construct(StringCollection $ids = null, StringVal $country = null, IntVal $limit = null)
    {
        $this->ids = $ids;
        $this->country = $country;
        $this->limit = $limit;
        // todo offset?
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
        return new self($this->ids, $this->country, $limit);
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
        return new self($ids, $this->country, $this->limit);
    }

    /**
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(StringVal $country = null)
    {
        return new self($this->ids, $country, $this->limit);
    }

    /** Returns API compatible assoc array for query building
     * @return array
     */
    public function toApiArray()
    {
        return [];
    }

    public function createApiHash()
    {
        return new StringVal(md5(serialize($this->toApiArray())));
    }
}
