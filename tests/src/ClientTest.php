<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Client\Client;
use Packetery\SDK\Client\ClientException;
use Packetery\SDK\Config;
use Packetery\SDK\Feed\FeedServiceBrain;
use Packetery\SDK\Storage\MemoryStorage;
use Packetery\Utils\Json;

class ClientTest extends BaseTest
{

    public function testDownload()
    {
        $baseConf = [
            'parameters' =>
                [
                    'api' => [
                        'baseUrl' => 'http://www.nonextsdomain.lh/api',
                        'key' => 'xxxxxx',
                        'timeout' => 3
                    ]
                ]
        ];

        $this->assertException(
            ClientException::class,
            function () use ($baseConf) {
                $baseConf['parameters']['api']['baseUrl'] = 'http://www.zasilkovna.cz/api';
                $client = new Client(new Config($baseConf));
                $client->getSimpleCarriers();
            },
            'Client didnt throw ClientException'
        );

        $this->assertException(
            ClientException::class,
            function () use ($baseConf) {
                $baseConf['parameters']['api']['baseUrl'] = 'http://www.nonextsdomain.lh/api';
                $client = new Client(new Config($baseConf));
                $client->getSimpleCarriers();
            },
            'Client didnt throw ClientException'
        );

        $baseConf['parameters']['api']['baseUrl'] = 'http://www.nonextsdomain.lh/api';
        $client = new Client(new Config($baseConf));
        $brain = new FeedServiceBrain($client, $this->createCacheFileStorage(), $this->config);
        $generatorResult = $brain->getSimpleCarrierGenerator();
        $this->assertCount(0, $generatorResult);

        $client = new Client($this->config);
        $body = $client->getSimpleCarriers();
        $this->assertTrue(!empty($body), 'body is empty');

        $brain = $this->container->getFeedServiceBrain();
        $decoded = $this->callPrivateMethod($brain, 'decodeJsonContent', [$body]);
        $this->assertNotEmpty($decoded);
        $decoded = $this->callPrivateMethod($brain, 'decodeJsonContent', ['']);
        $this->assertNull($decoded);
    }
}
