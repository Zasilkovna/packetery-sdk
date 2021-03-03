<?php

namespace Packetery\SDK\Storage;

use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;

class FileStorage implements IStorage
{
    /** @var string */
    private $dir;

    /**
     * FileStorage constructor.
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;

        if (!is_writable($this->dir)) {
            throw new InvalidArgumentException('temp folder is not writable');
        }
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
        $hash = $this->createHash($key);
        return $this->dir . DIRECTORY_SEPARATOR . $hash;
    }

    private function createHash($key)
    {
        return md5((string)$key);
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
}
