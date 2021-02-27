<?php

/** @var \Packetery\SDK\Container $container */
$container = require __DIR__ . '/autoload.php';
$carrierIterator = $container->getDatabaseFeedService()->getHDCarriersByCountry('cz');

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
