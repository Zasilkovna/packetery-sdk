<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Config;

class ConfigTest extends BaseTest
{

    public function testDefaults()
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

        $config = new Config($baseConf);
        $this->assertEquals(11, $config->getFeedCacheExpirationSeconds());

        $baseConf = [
            'parameters' => [
                'tempFolder' => __DIR__ . '/../temp',
                'api' => [
                    'key' => 'yourApiKey', // see client.packeta.com
                    'baseUrl' => 'http://www.zasilkovna.cz/api',
                    'timeout' => 30,
                ],
                'feedCacheExpirationSeconds' => null
            ]
        ];

        $config = new Config($baseConf);
        $this->assertEquals(null, $config->getFeedCacheExpirationSeconds());

        $baseConf = [
            'parameters' => [
                'tempFolder' => __DIR__ . '/../temp',
                'api' => [
                    'key' => 'yourApiKey', // see client.packeta.com
                    'baseUrl' => 'http://www.zasilkovna.cz/api',
                    'timeout' => 30,
                ]
            ]
        ];

        $config = new Config($baseConf);
        $this->assertEquals(60 * 10, $config->getFeedCacheExpirationSeconds());
    }
}
