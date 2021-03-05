<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\SDK\Cache;
use Packetery\SDK\Storage\MemoryStorage;
use Packetery\Utils\FS;

class CacheTest extends BaseTest
{

    public function testLoad()
    {
        $storage = $this->createCacheFileStorage();
        $cache = new Cache($storage);

        $content = md5(microtime() . rand());
        $key = ('key');
        $contentLoaded = $cache->load(
            $key,
            function () use ($content) {
                return ($content);
            },
            0
        );

        $this->assertTrue($cache->exists($key), 'cache file must exist');
        $this->assertTrue($cache->isExpired($key, -1), 'cache file must be expired');
        $this->assertFalse($cache->isExpired($key, 20), 'cache file must NOT be expired');
        $this->assertEquals($content, (string)$contentLoaded, 'incorrect content');

        $content2 = md5(microtime() . rand());
        $contentLoaded = $cache->load(
            $key,
            function () use ($content2) {
                return ($content2);
            },
            100
        );

        $this->assertEquals((string)$contentLoaded, $content, 'cache invalidated when it should not');
        $this->assertNotEquals((string)$contentLoaded, $content2, 'cache loaded incorrect content');

        $content3 = md5(microtime() . rand());
        $contentLoaded = $cache->load(
            $key,
            function () use ($content3) {
                return ($content3);
            },
            -1
        );

        $this->assertEquals((string)$contentLoaded, $content3, 'cache was not invalidated');

        $this->assertTrue($cache->exists($key));
        $storage = $this->createCacheFileStorage('testCache2');
        $cache2InSameDir = new Cache($storage);

        $content4 = md5(microtime() . rand());
        $contentLoadedOther = $cache2InSameDir->load($key, function () use ($content4) {
            return $content4;
        });

        $contentLoadedOther = $cache2InSameDir->load($key, function () use ($content4) {
            return 'failed';
        });

        $this->assertEquals($contentLoadedOther, $content4, 'cache was not invalidated');

        $contentLoaded = $cache->load(
            $key,
            function () use ($content3) {
                return 'new content that must not be there';
            },
            1000
        );

        $this->assertEquals($contentLoaded, $content3, 'cache was not invalidated');

        $this->assertTrue($cache->exists($key));
        FS::removeFiles($this->config->getTempFolder() . '/*');
        $this->assertFalse($cache->exists($key));
    }

    public function testMemoryStorage()
    {
        $key = 'key';
        $memory = new MemoryStorage();
        $memory->set($key, 'test');
        $content = $memory->get($key);

        $this->assertEquals($content, 'test');
        $this->assertTrue($memory->exists($key));
        $memory->remove($key);
        $this->assertFalse($memory->exists($key));

        $content = $memory->get($key);
        $this->assertEquals(null, $content);
    }
}
