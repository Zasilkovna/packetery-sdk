<?php

namespace Packetery\SDK\Client;

use Packetery\SDK\Config;
use Packetery\SDK\Feed\CarrierFilter;

class Client
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getSimpleCarriers()
    {
        $params = [];
        $params['address-delivery'] = '1'; // nevykreslí branches a do carriers dá De Hermes HD i DE Hermes PP bez seznamu pickup pointů

        $url = $this->createUrl('branch.json', $params);

        $content = $this->get($url);

        return ($content ?: null);
    }

    private function get($url)
    {
        $ctx = stream_context_create(
            [
                'http' =>
                    [
                        'method' => 'GET',
                        'header' => [
                            'Connection: close',
                        ],
                        'timeout' => $this->config->getApiTimeout(),  // Seconds
                    ]
            ]
        );

        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new ClientException($message, $severity);
            }
        );

        try {
            return file_get_contents((string)$url, false, $ctx);
        } catch (\Exception $exception) {
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            restore_error_handler();
        }
    }

    private function createUrl($endpoint, array $params)
    {
        $query = http_build_query($params);
        $url = "{$this->config->getApiBaseUrl()}/v4/{$this->config->getApiKey()}/{$endpoint}?{$query}";
        return $url;
    }
}
