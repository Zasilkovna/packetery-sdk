<?php

require __DIR__ . '/autoload.php';

$container = \Packetery\SDK\Container::create(require __DIR__ . '/config.php');

$feedService = $container->getApiFeedService();
echo "<h2>SDK examples</h2>";
echo "<br>Example 1 - using db layer - address delivery carriers for Hungary<br>";


$carrierCollection = $feedService->getAddressDeliveryCarriersByCountry('hu');

echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrierCollection = $feedService->getAddressDeliveryCarriersByCountry("hu");

echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

')));
