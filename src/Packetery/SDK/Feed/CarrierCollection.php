<?php

namespace Packetery\SDK\Feed;

use Packetery\SDK\AbstractCollection;

class CarrierCollection extends AbstractCollection
{

    /**
     * @return string
     */
    public function getItemClass()
    {
        return Carrier::class;
    }

    /**
     * @return \Packetery\SDK\Feed\Carrier|null
     */
    public function first()
    {
        return parent::first();
    }
}
