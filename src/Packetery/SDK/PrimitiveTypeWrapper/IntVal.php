<?php

namespace Packetery\SDK\PrimitiveTypeWrapper;

use Packetery\Domain\InvalidArgumentException;

class IntVal
{
    /** @var int */
    private $value;

    /**
     * @param bool $value
     */
    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException();
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function parse($value)
    {
        if ($value instanceof self) {
            $value = $value->getValue();
        }

        return new self((int)$value);
    }
}
