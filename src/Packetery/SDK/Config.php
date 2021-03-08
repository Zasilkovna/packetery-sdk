<?php

namespace Packetery\SDK;

use Packetery\Utils\Arrays;

/**
 * Config array wrapper
 */
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

    /**
     * @return int
     */
    public function getFeedCacheExpirationSeconds()
    {
        return $this->getParameter(['feedCacheExpirationSeconds'], 60 * 10);
    }

    /**
     * @return int
     */
    public function getApiTimeout()
    {
        return $this->getParameter(['api', 'timeout'], 30);
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->getParameter(['api', 'baseUrl']);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter(['api', 'key']);
    }

    /**
     * @return sring
     */
    public function getTempFolder()
    {
        return $this->getParameter(['tempFolder']);
    }

    /**
     * @param array $keys
     * @param mixed|null $default
     * @return mixed
     */
    private function getParameter(array $keys, $default = null)
    {
        if (func_num_args() < 2) {
            return Arrays::getValue($this->config['parameters'], $keys);
        }

        return Arrays::getValue($this->config['parameters'], $keys, $default);
    }
}
