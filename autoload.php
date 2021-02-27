<?php

require_once __DIR__ . '/src/Packetery/Domain/exception.php';

global $packeteryContainer;

if (empty($packeteryContainer)) {

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
    
    $packeteryContainer = new \Packetery\SDK\Container(require __DIR__ . '/config.php');
}

return $packeteryContainer;
