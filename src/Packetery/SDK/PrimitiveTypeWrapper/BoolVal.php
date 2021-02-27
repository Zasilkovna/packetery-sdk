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

    public static function parse($value)
    {
        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }

        return new self((bool)$value);
    }

    public function getValue()
    {
        return $this->value;
    }
}
