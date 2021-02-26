<?php

namespace Packetery\SDK;

use Packetery\Utils\Arrays;

class Config
{
    /** @var array */
    private $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getApiBaseUrl()
    {
        return $this->getParameter(['apiBaseUrl']);
    }

    public function getApiKey()
    {
        return $this->getParameter(['apiKey']);
    }

    public function getTempFolder()
    {
        return $this->getParameter(['tempFolder']);
    }

    public function getTablePrefix()
    {
        return $this->getParameter(['tablePrefix']);
    }

    private function getParameter(array $keys)
    {
        return Arrays::getValue($this->config['parameters'], $keys);
    }

    public function getConnection()
    {
        return $this->getParameter(['connection']);
    }
}
