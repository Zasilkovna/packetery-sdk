<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Container;
use Packetery\SDK\Feed\IFeedService;
use Packetery\Utils\Json;

class AutoloadTest extends BaseTest
{

    public function testClassAutoload()
    {
        $this->assertTrue(class_exists(Container::class), 'Container class was not loaded');
        $this->assertTrue(class_exists(Json::class), 'Json class was not loaded');
        $this->assertTrue(class_exists(InvalidArgumentException::class), 'exceptoins not loaded');
        $this->assertTrue(interface_exists(IFeedService::class), 'IFeedService not loaded');
        $this->assertTrue(defined('PACKETERY_SDK_VERSION'), 'PACKETERY_SDK_VERSION constant does not exist');
    }
}
