<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Container;
use Packetery\SDK\Feed\ApiFeedService;
use Packetery\SDK\Feed\FeedServiceBrain;

class ContainerTest extends BaseTest
{

    public function testMemory()
    {
        $baseConf = [
            'parameters' => [
                'tempFolder' => __DIR__ . '/../temp',
                'api' => [
                    'key' => 'yourApiKey', // see client.packeta.com
                    'baseUrl' => 'http://www.zasilkovna.cz/api',
                    'timeout' => 30,
                ],
                'feedCacheExpirationSeconds' => 11
            ]
        ];

        $container = Container::create($baseConf);
        $service = $container->getApiFeedService();
        $service2 = $container->getApiFeedService();
        $this->assertInstanceOf(ApiFeedService::class, $service);
        $this->assertInstanceOf(ApiFeedService::class, $service2);
        $this->assertTrue($service === $service2);
    }
}
