<?php

namespace Packetery\SDK;

use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class CallResult
{
    /** @var BoolVal */
    private $success;

    /** @var StringVal|null */
    private $responseBody;

    /**
     * @param StringVal|null $responseBody
     */
    public function __construct(BoolVal $boolVal, StringVal $responseBody = null)
    {
        $this->success = $boolVal;
        $this->responseBody = $responseBody;
    }

    /**
     * @return StringVal|null
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
}
