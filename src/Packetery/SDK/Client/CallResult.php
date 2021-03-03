<?php

namespace Packetery\SDK\Client;

class CallResult
{
    /** @var bool */
    private $success;

    /** @var string|null */
    private $responseBody;

    public function __construct($boolVal, $responseBody = null)
    {
        $this->success = $boolVal;
        $this->responseBody = $responseBody;
    }

    /**
     * @return string|null
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
}
