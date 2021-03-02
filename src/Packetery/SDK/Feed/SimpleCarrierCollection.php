<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\Collection;

class SimpleCarrierCollection extends Collection
{

    public function getItemClass()
    {
        return SimpleCarrier::class;
    }

    /**
     * @return \Packetery\SDK\Feed\SimpleCarrier|null
     */
    public function first()
    {
        return parent::first();
    }
}
