<?php

namespace Packetery\SDK\Storage;

use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;

/**
 * Stores key/value pair as file in given folder
 */
class FileStorage implements IStorage
{
    /** @var string */
    private $dir;

    /** @var string */
    private $name;

    /**
     * @param string $dir
     * @param string|null $name
     */
    public function __construct($dir, $name = null)
    {
        $this->dir = $dir;

        if (!is_writable($this->dir)) {
            throw new InvalidArgumentException('temp folder is not writable');
        }

        $this->name = ($name ?: 'default');
    }

    /**
     * @param string $key
     * @return string|null
     * @throws \Packetery\Domain\InvalidStateException
     */
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

    /**
     * @param string $key
     * @param string $content
     */
    public function set($key, $content)
    {
        $file = $this->getExistingFilePath($key);
        file_put_contents($file, $content);
    }

    /**
     * @param string $key
     * @return float|null
     */
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

    /**
     * @param string $key
     * @return string
     */
    private function getExistingFilePath($key)
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            @touch($file);
        }

        return $file;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getFilePath($key)
    {
        $hash = $this->createFilename($key);
        return $this->dir . DIRECTORY_SEPARATOR . $hash;
    }

    /**
     * @param string $key
     * @return string
     */
    private function createFilename($key)
    {
        return $this->name . '_' . $key . '.txt';
    }

    /**
     * @param string $key
     */
    function remove($key)
    {
        $file = $this->getFilePath($key);
        @unlink($file);
    }

    /**
     * @param string $key
     * @return bool
     */
    function exists($key)
    {
        return file_exists($this->getFilePath($key));
    }

    /**
     * @param string $tempFolder
     * @param string|null $name
     * @return \Packetery\SDK\Storage\FileStorage
     */
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
