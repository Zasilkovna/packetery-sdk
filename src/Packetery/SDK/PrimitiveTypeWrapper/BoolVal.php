<?php

namespace Packetery\SDK\PrimitiveTypeWrapper;

use Packetery\Domain\InvalidArgumentException;

class BoolVal
{
    /** @var bool */
    private $value;

    /**
     * @param bool $value
     */
    public function __construct($value)
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException();
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
