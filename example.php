<?php

/** @var \Packetery\SDK\Container $container */
$container = require __DIR__ . '/autoload.php';
$feedService = $container->getDatabaseFeedService();

// example 1
$carrierIterator = $feedService->getHomeDeliveryCarriersByCountry('cz');

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

// example 2
$filter = new \Packetery\SDK\Feed\BranchFilter();

$sample = new \Packetery\SDK\Feed\SimpleCarrierSample();
$sample->setCountry('sk');

$filter->setSimpleCarrierSample($sample);

$carrierIterator = $feedService->getSimpleCarriers($filter); // returns every possible sk carrier

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
