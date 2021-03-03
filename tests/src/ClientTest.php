<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Client\Client;
use Packetery\SDK\Client\ClientException;
use Packetery\Utils\Json;

class ClientTest extends BaseTest
{

    public function testDownload()
    {
        $this->assertException(
            ClientException::class,
            function () {
                $client = new Client(('http://www.zasilkovna.cz/api'), ('v4'), ('xxxxxx'));
                $client->getSimpleCarriers();
            },
            'Client didnt throw ClientException'
        );

        $client = new Client(($this->config->getApiBaseUrl()), ('v4'), ($this->config->getApiKey()));
        $result = $client->getSimpleCarriers();

        $body = $result->getResponseBody();
        $this->assertTrue(!empty($body), 'body is empty');
        Json::decode($body);
    }
}
