<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Client;
use Packetery\SDK\ClientException;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\Json;

class ClientTest extends BaseTest
{

    public function testDownload()
    {
        $this->assertException(ClientException::class, function () {
            $client = new Client(StringVal::create('http://www.zasilkovna.cz/api'), new StringVal('v4'), StringVal::create('xxxxxx'));
            $client->getSimpleCarriers();
        }, 'Client didnt throw ClientException');

        $client = new Client(StringVal::create($this->config->getApiBaseUrl()), new StringVal('v4'), StringVal::create($this->config->getApiKey()));
        $result = $client->getSimpleCarriers();

        $body = $result->getResponseBody();
        $this->assertTrue(!empty($body), 'body is empty');
        Json::decode($body);
    }
}
