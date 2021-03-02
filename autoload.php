<?php

require_once __DIR__ . '/src/Packetery/Domain/exception.php';

global $packeteryContainer;

if (empty($packeteryContainer)) {

    define('PACKETERY_SDK_VERSION', '1.0.0');

    spl_autoload_register(
        function ($className) {
            $className = ltrim($className, '\\');
            $parts = explode('\\', $className);
            $path = __DIR__ . '/src/' . implode('/', $parts) . '.php';
            if (is_file($path)) {
                require_once $path;
            }
        }
    );
    
    $packeteryContainer = new \Packetery\SDK\Container(new \Packetery\SDK\Config(require __DIR__ . '/config.php'));
}

return $packeteryContainer;
