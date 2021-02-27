<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidStateException;

class Duration
{
    /** @var \Packetery\SDK\Decimal */
    private $value;

    /** @var \Packetery\SDK\DurationUnit */
    private $unit;

    /**
     * Duration constructor.
     *
     * @param \Packetery\SDK\Decimal $value
     * @param \Packetery\SDK\DurationUnit $unit
     */
    public function __construct(Decimal $value, DurationUnit $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    public function toSeconds()
    {
        if ($this->unit->getValue() === DurationUnit::SECOND) {
            return $this->value;
        }

        throw new InvalidStateException('unit not supported');
    }

    public function minus(Duration $duration)
    {
        $seconds = $this->toSeconds();
        $valueSeconds = $duration->toSeconds();
        $diff = $seconds->minus($valueSeconds);

        return new Duration(Decimal::parse($diff), new DurationUnit(DurationUnit::SECOND));
    }

}
