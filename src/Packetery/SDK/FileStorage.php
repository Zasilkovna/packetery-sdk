<?php

namespace Packetery\SDK;

use Packetery\Domain\InvalidArgumentException;
use Packetery\Domain\InvalidStateException;
use Packetery\SDK\PrimitiveTypeWrapper\BoolVal;
use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

class FileStorage implements IStorage
{
    /** @var \Packetery\SDK\PrimitiveTypeWrapper\StringVal */
    private $dir;

    /**
     * FileStorage constructor.
     *
     * @param \Packetery\SDK\PrimitiveTypeWrapper\StringVal $dir
     */
    public function __construct(PrimitiveTypeWrapper\StringVal $dir)
    {
        $this->dir = $dir;

        if (!is_writable($this->dir)) {
            throw new InvalidArgumentException('cache folder is not writable');
        }
    }

    public function get(StringVal $key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            throw new InvalidStateException('does cache folder exist?');
        }

        return new StringVal($content);
    }

    public function set(StringVal $key, StringVal $content)
    {
        $file = $this->getExistingFilePath($key);
        file_put_contents($file, $content);
    }

    public function duration(StringVal $key)
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
        return new Duration(Decimal::parse($duration), new DurationUnit(DurationUnit::SECOND));
    }

    private function getExistingFilePath(StringVal $key)
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            @touch($file);
        }

        return $file;
    }

    private function getFilePath(StringVal $key)
    {
        $hash = $this->createHash($key);
        return $this->dir . DIRECTORY_SEPARATOR . $hash;
    }

    private function createHash(StringVal $key)
    {
        return md5((string)$key);
    }

    function remove(StringVal $key)
    {
        $file = $this->getFilePath($key);
        @unlink($file);
    }

    function exists(StringVal $key)
    {
        return file_exists($this->getFilePath($key));
    }
}
