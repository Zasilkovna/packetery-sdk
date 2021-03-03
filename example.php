<?php

require __DIR__ . '/autoload.php';

$container = \Packetery\SDK\Container::create(require __DIR__ . '/config.php');
$feedService = $container->getDatabaseFeedService();
$feedService->updateData();

echo "<h2>SDK usage examples</h2>";
echo "<br>Example 1 - home delivery carriers in Slovakia<br>";
$carrierIterator = $feedService->getHomeDeliveryCarriersByCountry('sk');

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo "<br>Example 2 - paging<br>";
$filter = new \Packetery\SDK\Feed\CarrierFilter();

$sample = new \Packetery\SDK\Feed\SimpleCarrierSample();
$sample->setCountry('cz');
$filter->setSimpleCarrierSample($sample);

$alreadyDisplayed = ['13']; // czpost
$filter->setExcludedIds(($alreadyDisplayed));
$filter->setLimit(100);

$carrierIterator = $feedService->getSimpleCarriers($filter); // returns every possible sk carrier that was not displayed

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo "<br>Example 3 - get by id<br>";
$carrier2 = $feedService->getSimpleCarrierById((string)$carrier->getId());
echo $carrier->getName();
echo "<br>";

echo "<br>Example 4 - multi datasource action<br>";
$carrierIterator = $feedService->getHomeDeliveryCarriers();
$carriers = iterator_to_array($carrierIterator);

echo "carrier count: " . count($carriers) . "<br>";
foreach ($carriers as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}
