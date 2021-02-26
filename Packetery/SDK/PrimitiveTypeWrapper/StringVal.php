<?php

namespace Packetery\SDK\PrimitiveTypeWrapper;

use Packetery\Domain\InvalidArgumentException;

/**
 * string type wrapper
 */
class StringVal
{
    /** @var string */
    private $value;

    /**
     * String constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Argument must be string. Type "' . gettype($value) . '" given');
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function equals(StringVal $value)
    {
        return $this->value === $value->getValue();
    }

    public function append($value)
    {
        return new StringVal($this->value . ((string)$value));
    }

    public static function parse($value)
    {
        return new self((string)$value);
    }

    public static function create($value)
    {
        return new self($value);
    }

    public static function createOrNull($value)
    {
        if (!is_string($value)) {
            return null;
        }

        return new self($value);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
