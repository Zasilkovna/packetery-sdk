<?php

namespace Packetery\Tests;

require __DIR__ . '/../autoload.php';

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Cache;
use Packetery\Utils\Arrays;
use Packetery\Utils\FS;
use Packetery\Utils\Json;

class UtilsTest extends BaseTest
{

    public function testArrays()
    {
        $data = [
            null => null,
            '' => 0,
            '' => null,
            'in_feed' => null,
            'id' => 13,
            'points' => [
                '1' => [
                    'code' => 'ABC123'
                ]
            ],
            'pointsArray' => [
                [
                    'code' => 'ABC123'
                ]
            ]
        ];

        $this->assertException(
            InvalidArgumentException::class,
            function () use ($data) {
                Arrays::getValue($data, ['points', '1', 'code', 'notexisting']);
            }
        );
        $this->assertException(
            InvalidArgumentException::class,
            function () use ($data) {
                Arrays::getValue($data, ['xxxxxxxxxxxxxx']);
            }
        );

        $this->assertEquals(Arrays::getValue($data, ['pointsArray', 0, 'code']), 'ABC123');
        $this->assertEquals(Arrays::getValue($data, ['points', '1', 'code', 'notexisting'], 'default'), 'default');
        $this->assertEquals(Arrays::getValue($data, ['points', '1', 'code', 'notexisting', 'ěěěěě', 1221], 'default2'), 'default2');
        $this->assertEquals(Arrays::getValue($data, ['xxxxxxxxxxxxxx'], 'default'), 'default');
        $this->assertEquals(Arrays::getValue($data, ['points']), $data['points']);
        $this->assertEquals(Arrays::getValue($data, ['points', '1', 'code']), 'ABC123');
        $this->assertEquals(Arrays::getValue($data, ['in_feed']), null);
        $this->assertEquals(Arrays::getValue($data, ['']), null);
        $this->assertEquals(Arrays::getValue($data, [null]), null);
        $this->assertEquals(Arrays::getValue($data, ['id']), 13);
    }

    public function testFS()
    {
        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*');
        $this->assertEmpty($filesNFolder, 'rglob not working');

        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*', GLOB_ONLYDIR);
        $this->assertEmpty($filesNFolder, 'rglob not working');

        $cache = $this->createCacheFileStorage();

        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*');
        $this->assertNotEmpty($filesNFolder, 'rglob not working'); // search for cache directory

        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*', GLOB_ONLYDIR);
        $this->assertNotEmpty($filesNFolder, 'rglob not working');

        $cache->set('key', 'content');

        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*');
        $this->assertNotEmpty($filesNFolder, 'rglob not working');
        $filtered = array_filter(
            $filesNFolder,
            function ($file) {
                if (is_file($file)) {
                    return true;
                }

                return false;
            }
        );

        $this->assertNotEmpty($filtered, 'recursive behaviour not working');
        $this->assertCount(1, $filtered, 'recursive behaviour not working');

        $filesNFolder = FS::rglob($this->config->getTempFolder() . '/*', GLOB_ONLYDIR);
        $this->assertNotEmpty($filesNFolder, 'rglob not working');
        $filtered = array_filter(
            $filesNFolder,
            function ($file) {
                if (is_file($file)) {
                    return true;
                }

                return false;
            }
        );

        $this->assertEmpty($filtered, 'recursive behaviour not working');

        $files = FS::rglob($this->config->getTempFolder() . '/myNonExistingFolder_asdjkasjdlkaslkdalksdlkasdjasdlkaslkddasoieieu/*');
        $this->assertEmpty($files);

        $files = FS::rglob($this->config->getTempFolder() . '/myNonExistingFolder_asdjkasjdlkaslkdalksdlkasdjasdlkaslkddasoieieu/*', GLOB_ONLYDIR);
        $this->assertEmpty($files);
    }

    public function testJson()
    {
        $data = [
            'key' => 1
        ];

        $this->assertEquals(Json::encode($data), json_encode($data, JSON_FORCE_OBJECT));
        $this->assertEquals(Json::encode($data, Json::FORCE_ARRAY), json_encode($data));
        $this->assertEquals(Json::encode($data), '{"key":1}');
        $this->assertEquals(Json::decode('{"key":1}', Json::FORCE_ARRAY), $data);
    }
}
