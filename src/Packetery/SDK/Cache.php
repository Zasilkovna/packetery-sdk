<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidArgumentException;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;
use Packetery\Utils\FS;

class Cache
{
    const OPTION_EXPIRATION = 'expiration';

    /** @var \Packetery\SDK\IStorage */
    private $storage;

    /**
     * @param \Packetery\SDK\IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public static function createCacheFolder(StringVal $tempFolder)
    {
        if (!is_dir($tempFolder->getValue())) {
            throw new InvalidArgumentException('Not a folder: ' . $tempFolder);
        }

        if (!is_writable($tempFolder->getValue())) {
            throw new InvalidArgumentException('Folder not writable: ' . $tempFolder);
        }

        $finalDir = $tempFolder->append('/cache');
        if (!is_dir($finalDir)) {
            mkdir($finalDir);
        }

        return $finalDir;
    }

    public function exists(StringVal $key)
    {
        return $this->storage->exists($key);
    }

    public function isExpired(StringVal $key, Duration $maxDuration)
    {
        $duration = $this->storage->duration($key);
        if ($duration) {
            $diff = $maxDuration->minus($duration);
            if ($diff->toSeconds()->lt(Decimal::parse(0))) {
                return true;
            }
        }

        return false;
    }

    public function load(StringVal $key, callable $factory, array $options = null)
    {
        if ($options !== null) {
            if (isset($options[self::OPTION_EXPIRATION])) {
                $maxDuration = new Duration(Decimal::parse($options[self::OPTION_EXPIRATION]), new DurationUnit(DurationUnit::SECOND));
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
        $tempFolder = StringVal::parse($tempFolder);
        if (is_dir($tempFolder->append('/cache'))) {
            $files = FS::rglob($tempFolder->append('/cache/*'), GLOB_NOSORT);

            foreach ($files as $file) {
                if (is_dir($file) || $file === '.' || $file === '..') {
                    continue;
                }

                unlink($file);
            }
        }
    }
}
