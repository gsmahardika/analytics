<?php

use Agung\Analytics\Analytics;
use Agung\Analytics\AnalyticsClient;
use Agung\Analytics\AnalyticsClientFactory;

require __DIR__.'/vendor/autoload.php';

$config = require __DIR__.'/config.php';
$factory = new AnalyticsClientFactory($config);

$client = new AnalyticsClient($factory->getService(), $factory->getCache());
$client->setCacheLifeTime($config['cache_lifetime']);

$analytics = new Analytics($client, $config['view_id']);

$today = date('Y-m-d');
$startDate = new \DateTime($today);
$endDate = new \DateTime($today);

$period = new \stdClass;
$period->startDate = $startDate->modify('first day of this month');
$period->endDate = $endDate->modify('last day of this month');

echo '<pre>';
print_r($analytics->fetchVisitorsAndPageViews($period));
echo '</pre>';
