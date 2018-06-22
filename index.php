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

$period = new \stdClass;
$period->startDate = new \DateTime('2017-04-01');
$period->endDate = new \DateTime('2017-04-30');

echo '<pre>';
print_r($analytics->fetchVisitorsAndPageViews($period));
echo '</pre>';
