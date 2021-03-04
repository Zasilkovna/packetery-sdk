<?php

require_once __DIR__ . '/src/Packetery/Domain/exception.php';

if (!defined('PACKETERY_SDK_VERSION')) {

    define('PACKETERY_SDK_VERSION', '1.0.0');

    spl_autoload_register(
        function ($className) {
            $className = ltrim($className, '\\');
            $parts = explode('\\', $className);
            $path = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
            if (is_file($path)) {
                require_once $path;
            }
        }
    );
}

require_once __DIR__ . '/src/PacketeryDibi/dibi.php';
require_once __DIR__ . '/src/PacketeryDibi/exceptions.php';
require_once __DIR__ . '/src/PacketeryDibi/interfaces.php';
require_once __DIR__ . '/src/PacketeryDibi/HashMap.php';
