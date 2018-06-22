<?php

namespace AgungMaxsol\Analytics;

use Google_Client;
use Google_Service_Analytics;

class AnalyticsClientFactory
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getService()
    {
        $client = new Google_Client();
        $client->setScopes([Google_Service_Analytics::ANALYTICS_READONLY]);
        $client->setAuthConfig($this->config['credentials']);

        return new Google_Service_Analytics($client);
    }
}
