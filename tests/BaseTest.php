<?php

namespace Packetery\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function tearDown()
    {
        $files = glob(__DIR__ . '/temp/*', GLOB_NOSORT);
        foreach ($files as $file) {
            // todo extra dir for Cache
            if ($file === '.' || $file === '..' || is_dir($file)) {
                continue;
            }

            unlink($file);
        }
    }
}
