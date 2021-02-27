<?php

namespace Packetery\SDK;

use Packetery\SDK\PrimitiveTypeWrapper\IntVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class Decimal
{
    /** @var StringVal */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(StringVal $value)
    {
        $this->value = $value;
    }

    public static function parse($value)
    {
        return self::create((string)$value);
    }

    public static function create($value)
    {
        return new self(new StringVal($value));
    }

    public function getValue()
    {
        return $this->value;
    }

    /** Less than
     * @param \Packetery\SDK\Decimal $decimal
     * @return bool
     */
    public function lt(Decimal $decimal)
    {
        return $this->value->getValue() < $decimal->value->getValue();
    }

    public function minus(Decimal $decimal, IntVal $scale = null)
    {
        $added = bcadd($decimal->value->getValue(), $decimal->getValue() * (-1), $scale->getValue());
        return Decimal::create($added);
    }
}
