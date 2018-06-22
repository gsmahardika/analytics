<?php

use AgungMaxsol\Analytics\Analytics;
use AgungMaxsol\Analytics\AnalyticsClient;
use AgungMaxsol\Analytics\AnalyticsClientFactory;

require __DIR__.'/vendor/autoload.php';

$config = require __DIR__.'/config.php';
$factory = new AnalyticsClientFactory($config);
$client = new AnalyticsClient($factory->getService());
$analytics = new Analytics($client, $config['view_id']);

$period = new \stdClass;
$period->startDate = new \DateTime('2017-04-01');
$period->endDate = new \DateTime('2017-04-30');

echo '<pre>';
print_r($analytics->fetchVisitorsAndPageViews($period));
echo '</pre>';
