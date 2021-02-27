<?php

/** @var \Packetery\SDK\Container $container */
$container = require __DIR__ . '/autoload.php';
$carrierIterator = $container->getDatabaseFeedService()->getSimpleCarriersByCountry('cz');
//$carrierIterator = $container->getDatabaseFeedService()->getHDCarrierById('13');
//$carrierIterator = $container->getDatabaseFeedService()->getHDCarriers();

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
