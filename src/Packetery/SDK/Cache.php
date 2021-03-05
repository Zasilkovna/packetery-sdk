<?php

namespace Packetery\SDK;

use Packetery\SDK\Storage\IStorage;

class Cache
{
    /** @var \Packetery\SDK\Storage\IStorage */
    private $storage;

    /**
     * @param \Packetery\SDK\Storage\IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
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

    /**
     * @param $key
     * @param callable $factory It is called when no content is found
     * @param int|null $expirationSeconds
     * @return mixed|null
     * @throws \Packetery\Domain\InvalidStateException
     */
    public function load($key, callable $factory, $expirationSeconds = null)
    {
        if ($expirationSeconds !== null) {
            $maxDuration = $expirationSeconds;
            if ($this->isExpired($key, $maxDuration)) {
                $this->storage->remove($key);
            }
        }

        $content = $this->storage->get($key);

        if ($content === null) {
            $content = call_user_func_array($factory, []);
            $this->storage->set($key, $content);
        }

        return $content;
    }
}
