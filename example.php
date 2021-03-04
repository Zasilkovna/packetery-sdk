<?php

require __DIR__ . '/autoload.php';

//$container = \Packetery\SDK\Container::create(require __DIR__ . '/config.php');
$container = \Packetery\SDK\Container::create(require __DIR__ . '/config.php', new \Packetery\SDK\Database\PdoDriver());
$feedService = $container->getDatabaseFeedService();
$feedService->updateData();

echo "<h2>SDK usage examples</h2>";
echo "<br>Example 1 - home delivery carriers in Slovakia<br>";
$carrierIterator = $feedService->getAddressDeliveryCarriersByCountry('sk');

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

echo "<br>Example 2 - paging<br>";
$filter = new \Packetery\SDK\Feed\CarrierFilter();
$filter->buildSample('cz', null, true);
$alreadyDisplayed = ['13']; // czpost
$filter->setExcludedIds($alreadyDisplayed);
$filter->setLimit(100);

$carrierIterator = $feedService->getSimpleCarriers($filter); // returns every possible cz carrier in latest feed that was not displayed

foreach ($carrierIterator as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

if ($carrier) {
    echo "<br>Example 3 - get by id<br>";
    $carrier2 = $feedService->getSimpleCarrierById((string)$carrier->getId());
    echo $carrier->getName();
    echo "<br>";
}

$feedService = $container->getApiFeedService();
echo "<br>Example 4 - no db layer used - address delivery carriers for Hungary<br>";
$carrierCollection = $feedService->getAddressDeliveryCarriersByCountry('hu');

echo "carrier count: " . count($carrierCollection) . "<br>";
foreach ($carrierCollection as $carrier) {
    echo $carrier->getName();
    echo "<br>";
}

class MyCustomDriver extends \Packetery\SDK\Database\MysqliDriver {

    /** @var \mysqli */
    protected $connection;

    public function __construct(mysqli $mylink)
    {
        $this->connection = $mylink;
    }
}

echo "<br>Example 5 - custom driver<br>";

$config = require __DIR__ . '/config.php';
$params = $config['parameters']['connection'];
$myDbLink = mysqli_connect($params['host'], $params['user'], $params['password'], $params['database'], $params['port']);

$myCustomDriverInstace = new MyCustomDriver($myDbLink);

$newContainer = \Packetery\SDK\Container::create($config, $myCustomDriverInstace); // SDK will use your connection
$carrier = $newContainer->getDatabaseFeedService()->getSimpleCarrierById('16');

echo "carrier count using your connection: " . count($carrierCollection) . "<br>";
