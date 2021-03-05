<?php

namespace Packetery\SDK\Storage;

use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;

class FileStorage implements IStorage
{
    /** @var string */
    private $dir;

    /** @var string */
    private $name;

    public function __construct($dir, $name = null)
    {
        $this->dir = $dir;

        if (!is_writable($this->dir)) {
            throw new InvalidArgumentException('temp folder is not writable');
        }

        $this->name = $name ?: 'default';
    }

    public function get($key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            throw new InvalidStateException('does cache folder exist?');
        }

        return ($content);
    }

    public function set($key, $content)
    {
        $file = $this->getExistingFilePath($key);
        file_put_contents($file, $content);
    }

    public function duration($key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $duration = filemtime($file);
        if ($duration === false) {
            return null;
        }

        $duration = time() - $duration;
        return (float)$duration;
    }

    private function getExistingFilePath($key)
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            @touch($file);
        }

        return $file;
    }

    private function getFilePath($key)
    {
        $hash = $this->createFilename($key);
        return $this->dir . DIRECTORY_SEPARATOR . $hash;
    }

    private function createFilename($key)
    {
        return $this->name . '_' . $key . '.txt';
    }

    function remove($key)
    {
        $file = $this->getFilePath($key);
        @unlink($file);
    }

    function exists($key)
    {
        return file_exists($this->getFilePath($key));
    }

    public static function createCacheFileStorage($tempFolder, $name = null)
    {
        if (!is_dir($tempFolder)) {
            throw new InvalidArgumentException('Not a folder: ' . $tempFolder);
        }

        if (!is_writable($tempFolder)) {
            throw new InvalidArgumentException('Folder not writable: ' . $tempFolder);
        }

        $finalDir = $tempFolder . '/cache';
        if (!is_dir($finalDir)) {
            mkdir($finalDir);
        }

        return new FileStorage($finalDir, $name);
    }
}
