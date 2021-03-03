<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\Storage\IStorage;
use Packetery\Utils\FS;

class Cache
{
    const OPTION_EXPIRATION = 'expiration';

    /** @var \Packetery\SDK\Storage\IStorage */
    private $storage;

    /**
     * @param \Packetery\SDK\Storage\IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public static function createCacheFolder($tempFolder)
    {
        if (!is_dir($tempFolder)) {
            throw new InvalidArgumentException('Not a folder: ' . $tempFolder);
        }

        if (!is_writable($tempFolder)) {
            throw new InvalidArgumentException('Folder not writable: ' . $tempFolder);
        }

        $finalDir = $tempFolder . ('/cache');
        if (!is_dir($finalDir)) {
            mkdir($finalDir);
        }

        return $finalDir;
    }

    public function exists($key)
    {
        return $this->storage->exists($key);
    }

    /**
     * @param string $key
     * @param int $maxDuration duration in seconds
     * @return bool
     * @throws \Packetery\Domain\InvalidStateException
     */
    public function isExpired($key, $maxDuration)
    {
        $duration = $this->storage->duration($key);
        if ($duration !== null) {
            if ($maxDuration < $duration) {
                return true;
            }
        }

        return false;
    }

    public function load($key, callable $factory, array $options = null)
    {
        if ($options !== null) {
            if (isset($options[self::OPTION_EXPIRATION])) {
                $maxDuration = $options[self::OPTION_EXPIRATION];
                if ($this->isExpired($key, $maxDuration)) {
                    $this->storage->remove($key);
                }
            }
        }

        $content = $this->storage->get($key);

        if ($content === null) {
            $content = call_user_func_array($factory, []);
            $this->storage->set($key, $content);
        }

        return $content;
    }

    public static function clearAll($tempFolder)
    {
        if (is_dir($tempFolder . ('/cache'))) {
            $files = FS::rglob($tempFolder . ('/cache/*'), GLOB_NOSORT);

            foreach ($files as $file) {
                if (is_dir($file) || $file === '.' || $file === '..') {
                    continue;
                }

                unlink($file);
            }
        }
    }
}
