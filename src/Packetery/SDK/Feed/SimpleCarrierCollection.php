<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\AbstractCollection;

class SimpleCarrierCollection extends AbstractCollection
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
