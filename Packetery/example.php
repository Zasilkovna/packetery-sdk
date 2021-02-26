<?php

/** @var \Packetery\SDK\Container $container */
$container = require __DIR__ . '/autoload.php';
$collection = $container->getDatabaseFeedService()->getHDCarriersByCountry('cz');
