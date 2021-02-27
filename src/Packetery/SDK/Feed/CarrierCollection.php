<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Collection;

class CarrierCollection extends Collection
{

    public function getItemClass()
    {
        return Carrier::class;
    }
}
