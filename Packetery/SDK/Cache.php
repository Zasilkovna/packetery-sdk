<?php

namespace Packetery\SDK;

use Packetery\SDK\PrimitiveTypeWrapper\StringVal;

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

    public function exists(StringVal $key)
    {
        return $this->storage->exists($key);
    }

    public function isExpired(StringVal $key, Duration $maxDuration)
    {
        $duration = $this->storage->duration($key);
        if ($duration) {
            $diff = $maxDuration->minus($duration);
            if ($diff->toSeconds()->lt(Decimal::create(0))) {
                return true;
            }
        }

        return false;
    }

    public function load(StringVal $key, callable $factory, array $options = null)
    {
        if ($options !== null) {
            if (isset($options[self::OPTION_EXPIRATION])) {
                $maxDuration = new Duration(Decimal::create($options[self::OPTION_EXPIRATION]), new DurationUnit(DurationUnit::SECOND));
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
}
