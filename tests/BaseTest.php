<?php

namespace Packetery\Tests;

use Packetery\SDK\Cache;
use Packetery\SDK\Config;
use Packetery\SDK\Decimal;
use Packetery\SDK\Duration;
use Packetery\SDK\DurationUnit;
use Packetery\SDK\FileStorage;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\FS;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /** @var \Packetery\SDK\Config */
    protected $config;

    protected function setUp()
    {
        parent::setUp();
        $this->config = new Config(require __DIR__ . '/config.php');
    }

    protected function tearDown()
    {
//        parent::tearDown();
//        $files = FS::rglob(__DIR__ . '/temp/*', GLOB_NOSORT);
//        foreach ($files as $file) {
//            if ($file === '.' || $file === '..' || is_dir($file)) {
//                continue;
//            }
//
//            unlink($file);
//        }
    }

    protected function createCacheFileStorage()
    {
        return new FileStorage(Cache::createCacheFolder(StringVal::create(__DIR__ . '/temp')));
    }

    protected function createDuration($decimal)
    {
        return new Duration(Decimal::parse($decimal), new DurationUnit(DurationUnit::SECOND));
    }

    protected function assertException($exceptionClass, callable $callback, $message)
    {
        $exception = null;

        try {
            call_user_func_array($callback, []);
        } catch (\Exception $exception) {}

        $this->assertInstanceOf($exceptionClass, $exception, $message);
    }
}
