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

    public function getFeedCacheExpirationSeconds()
    {
        return $this->getParameter(['feedCacheExpirationSeconds'], 60 * 10);
    }

    public function getApiTimeout()
    {
        return $this->getParameter(['api', 'timeout'], 30);
    }

    public function getApiBaseUrl()
    {
        return $this->getParameter(['api', 'baseUrl']);
    }

    public function getApiKey()
    {
        return $this->getParameter(['api', 'key']);
    }

    public function getTempFolder()
    {
        return $this->getParameter(['tempFolder']);
    }

    private function getParameter(array $keys, $default = null)
    {
        if (func_num_args() < 2) {
            return Arrays::getValue($this->config['parameters'], $keys);
        }

        return Arrays::getValue($this->config['parameters'], $keys, $default);
    }
}
