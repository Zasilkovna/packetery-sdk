<?php

namespace Packetery\Utils;

use Packetery\Domain\InvalidArgumentException;

class FS
{
    public static function rglob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::rglob($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }

    public static function removeFiles($pattern)
    {
        $files = self::rglob($pattern, GLOB_NOSORT);

        foreach ($files ?: [] as $file) {
            if (is_dir($file) || $file === '.' || $file === '..') {
                continue;
            }

            unlink($file);
        }
    }
}
