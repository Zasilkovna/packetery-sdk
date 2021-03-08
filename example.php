<?php

require __DIR__ . '/autoload.php';

$container = \Packetery\SDK\Container::create(require __DIR__ . '/config.php');
$feedService = $container->getApiFeedService();

echo "<h2>SDK examples - using PHP without database</h2>";

echo "<h4>address delivery carriers</h4>";
$carrierCollection = $feedService->getAddressDeliveryCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrierCollection = $feedService->getAddressDeliveryCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
')));

echo "<br><h4>address delivery carriers for Hungary</h4>";
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

echo "<br><h4>all pickup point carriers</h4>";
$carrierCollection = $feedService->getPickupPointCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrierCollection = $feedService->getPickupPointCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
')));

echo "<br><h4>pickup point carriers - czech republic</h4>";
$carrierCollection = $feedService->getPickupPointCarriersByCountry('cz');
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrierCollection = $feedService->getPickupPointCarriersByCountry("cz");
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
')));

echo "<br><h4>all carriers</h4>";
$carrierCollection = $feedService->getCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrierCollection = $feedService->getCarriers();
echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
')));

if ($carrier) {
    echo "<br><h4>carrier by id</h4>";
    $carrier = $feedService->getCarrierById($carrier->getId());
    echo $carrier->getName();
    echo "<br>";

    echo nl2br(str_replace(' ', '&nbsp;', htmlentities('
$container = \Packetery\SDK\Container::create(require __DIR__ . "/config.php");
$feedService = $container->getApiFeedService();
$carrier = $feedService->getCarrierById($carrier->getId());
echo $carrier->getName();
')));
}
