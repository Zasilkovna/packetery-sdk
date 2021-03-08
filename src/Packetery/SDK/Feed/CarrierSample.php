<?php

namespace Packetery\SDK\Feed;

use Packetery\Domain\InvalidStateException;

/**
 * Sample for CarrierFilter
 */
class CarrierSample extends Carrier
{
    public function __construct()
    {
    }

    /**
     * @internal
     * @return string|null
     */
    public function getId()
    {
        throw new InvalidStateException('not implemented');
    }

    public function setId($id = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     * @return string|null
     */
    public function getName()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setName($name = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function isApiAllowed()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setApiAllowed($apiAllowed = null)
    {
        throw new InvalidStateException('not implemented');
    }

    public function isSeparateHouseNumber()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setSeparateHouseNumber($separateHouseNumber = null)
    {
        throw new InvalidStateException('not implemented');
    }

    public function isRequiresEmail()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setRequiresEmail($requiresEmail = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function isRequiresPhone()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setRequiresPhone($requiresPhone = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function isRequiresSize()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setRequiresSize($requiresSize = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function isDisallowsCod()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setDisallowsCod($disallowsCod = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function getCurrency()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setCurrency($currency = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function getMaxWeight()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setMaxWeight($maxWeight = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function getLabelRouting()
    {
        throw new InvalidStateException('not implemented');
    }

    public function setLabelRouting($labelRouting = null)
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function getLabelName()
    {
        throw new InvalidStateException('not implemented');
    }

    /**
     * @internal
     */
    public function setLabelName($labelName = null)
    {
        throw new InvalidStateException('not implemented');
    }
}
