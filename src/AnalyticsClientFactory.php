<?php

namespace Agung\Analytics;

use Google_Client;
use Google_Service_Analytics;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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

    public function getCache()
    {
        $adapter = new Local($this->config['cache_path']);

        return new Filesystem($adapter);
    }
}
