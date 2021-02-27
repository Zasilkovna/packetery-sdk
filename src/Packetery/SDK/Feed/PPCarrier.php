<?php

namespace Packetery\SDK\Feed;

/**
 * Represents complex carrier returned by point.json Packeta endpoint. Such carriers contain list of pickup points.
 * todo implement PPCarrierPointIterator and PPCarrierPoint object wrapper
 */
class PPCarrier extends Carrier
{
    /** @var \Iterator */
    private $points;
}
