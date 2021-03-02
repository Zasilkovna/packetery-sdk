<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Cache;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class CacheTest extends BaseTest
{

    public function testLoad()
    {
        $storage = $this->createCacheFileStorage();
        $cache = new Cache($storage);

        $content = md5(microtime() . rand());
        $key = StringVal::create('key');
        $contentLoaded = $cache->load(
            $key,
            function () use ($content) {
                return StringVal::parse($content);
            },
            [
                Cache::OPTION_EXPIRATION => 0
            ]
        );

        $this->assertTrue($cache->exists($key), 'cache file must exist');
        $this->assertTrue($cache->isExpired($key, $this->createDuration(-1)), 'cache file must be expired');
        $this->assertFalse($cache->isExpired($key, $this->createDuration(20)), 'cache file must NOT be expired');
        $this->assertEquals($content, (string)$contentLoaded, 'incorrect content');

        $content2 = md5(microtime() . rand());
        $contentLoaded = $cache->load(
            $key,
            function () use ($content2) {
                return StringVal::parse($content2);
            },
            [
                Cache::OPTION_EXPIRATION => 100
            ]
        );

        $this->assertEquals((string)$contentLoaded, $content, 'cache invalidated when it should not');
        $this->assertNotEquals((string)$contentLoaded, $content2, 'cache loaded incorrect content');

        $content3 = md5(microtime() . rand());
        $contentLoaded = $cache->load(
            $key,
            function () use ($content3) {
                return StringVal::parse($content3);
            },
            [
                Cache::OPTION_EXPIRATION => -1
            ]
        );

        $this->assertEquals((string)$contentLoaded, $content3, 'cache was not invalidated');

        $this->assertTrue($cache->exists($key));
        Cache::clearAll($this->config->getTempFolder());
        $this->assertFalse($cache->exists($key));
    }
}
