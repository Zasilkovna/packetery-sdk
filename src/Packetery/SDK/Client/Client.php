<?php

namespace Packetery\SDK\Client;

use Packetery\SDK\Feed\CarrierFilter;

class Client
{
    /** @var string */
    private $baseUrl;

    /** @var string */
    private $version;

    /** @var string */
    private $apiKey;

    /**
     * @param string $baseUrl
     * @param string $version
     * @param string $apiKey
     */
    public function __construct($baseUrl, $version, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->version = $version;
        $this->apiKey = $apiKey;
    }

    /**
     * @return \Packetery\SDK\Client\CallResult
     */
    public function getSimpleCarriers()
    {
        $params = [];
        $params['address-delivery'] = '1'; // nevykreslí branches a do carriers dá De Hermes HD i DE Hermes PP bez seznamu pickup pointů

        $url = $this->createUrl('branch.json', $params);

        $content = $this->get($url);

        return new CallResult(
            $content === false ? false : true,
            $content
        );
    }

    private function get($url)
    {
        $ctx = stream_context_create(
            [
                'http' =>
                    [
                        'method' => 'GET',
                        'timeout' => 30,  // Seconds
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
        $url = "{$this->baseUrl}/{$this->version}/{$this->apiKey}/{$endpoint}?{$query}";
        return $url;
    }
}
