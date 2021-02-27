<?php

namespace Packetery\SDK;

use Packetery\SDK\Feed\BranchFilter;
use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class Client
{
    /** @var StringVal */
    private $baseUrl;

    /** @var StringVal */
    private $version;

    /** @var StringVal */
    private $apiKey;

    /**
     * @param string $baseUrl
     * @param string $version
     * @param string $apiKey
     */
    public function __construct(StringVal $baseUrl, StringVal $version, StringVal $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->version = $version;
        $this->apiKey = $apiKey;
    }

    /**
     * @param \Packetery\SDK\Feed\BranchFilter|null $branchFilter
     * @return \Packetery\SDK\CallResult
     */
    public function getHDBranches(BranchFilter $branchFilter = null)
    {
        $params = $branchFilter->toApiArray();
        $params['address-delivery'] = '1';

        $url = $this->createUrl('/branch.json', $params);

        $content = $this->get($url);

        return new CallResult(
            new BoolVal($content === false ? false : true),
            StringVal::createOrNull($content)
        );
    }

    private function get(StringVal $url)
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

        return file_get_contents((string)$url, false, $ctx);
    }

    private function createUrl($endpoint, array $params)
    {
        $query = http_build_query($params);
        $url = StringVal::create($this->baseUrl)
            ->append('/')->append($this->version)->append('/')
            ->append($this->apiKey)->append('/')->append($endpoint)
            ->append('?')->append($query)
        ;

        return $url;
    }
}
