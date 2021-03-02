<?php

//phpinfo();

/** @var \Packetery\SDK\Container $container */
$container = require __DIR__ . '/autoload.php';
//$iterator = $container->getDatabaseFeedService()->getSimpleCarriersByCountry('sk');
$iterator = $container->getDatabaseFeedService()->getPickupPointCarriers();

foreach ($iterator as $row) {
//    echo print_r($row);
    echo $row->getName();
    echo "<br>";
}

//foreach ($carrierIterator as $carrier) {
//    echo $carrier->getName();
//    echo "<br>";
//}

//$conf = require __DIR__ . '/config.php';
//
//$driver = new \Packetery\SDK\Database\MysqliDriver();
//$driver->connect($conf['parameters']['connection']);
//$result = $driver->query('SELECT 1');
//
//$brain = $container->getFeedServiceBrain();
////$decoded = $brain->getSimpleCarrierGenerator();
////
////foreach ($decoded as $carrier) {
////    $container->getDatabaseRepository()->insertCarrier($carrier);
////}
//
//$result = $container->getDatabaseRepository()->findCarriers();
//
//$iterator = new \Packetery\SDK\Feed\SimpleCarrierIterator(new \IteratorIterator($result->getIterator()));
//$iterator = new \IteratorIterator($iterator);
//$iterator = new \IteratorIterator($iterator);
//$iterator = new \IteratorIterator($iterator);
//$iterator = new \IteratorIterator($iterator);
//
//foreach ($iterator as $row) {
//    echo $row->getName();
//    echo "<br>";
//}
