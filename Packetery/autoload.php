<?php

global $packeteryContainer;

if (empty($packeteryContainer)) {
    $packeteryContainer = new \Packetery\SDK\Container(require __DIR__ . '/config.php');
}

return $packeteryContainer;
