<?php

namespace AgungMaxsol\Analytics;

use Google_Service_Analytics;
use League\Flysystem\Filesystem;

class AnalyticsClient
{
    protected $service;
    protected $cache;
    protected $cacheLifeTime = 0;

    public function __construct(Google_Service_Analytics $service, Filesystem $cache)
    {
        $this->service = $service;
        $this->cache = $cache;
    }

    public function getAnalyticsService()
    {
        return $this->service;
    }

    public function setCacheLifeTime(int $cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;

        return $this;
    }

    public function performQuery(
        string $viewId,
        \DateTime $startDate,
        \DateTime $endDate,
        string $metrics,
        array $others = []
    ) {
        $cacheName = $this->determineCacheName(func_get_args());
        $exists = $this->cache->has($cacheName);

        if ($this->cacheLifeTime === 0 && $exists) {
            $this->cache->delete($cacheName);
        }

        if ($exists) {
            $contents = json_decode($this->cache->read($cacheName), true);

            if ((int) $contents['expired_at'] > time()) {
                return $contents['data'];
            }

            $this->cache->delete($cacheName);
        }

        $result = $this->service->data_ga->get(
            "ga:{$viewId}",
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $metrics,
            $others
        );

        while ($nextLink = $result->getNextLink()) {
            if (isset($others['max-results']) && count($result->rows) >= $others['max-results']) {
                break;
            }

            $options = [];
            parse_str(substr($nextLink, strpos($nextLink, '?') + 1), $options);
            $response = $this->service->data_ga->call('get', [$options], 'Google_Service_Analytics_GaData');

            if ($response->rows) {
                $result->rows = array_merge($result->rows, $response->rows);
            }

            $result->nextLink = $response->nextLink;
        }

        $cacheData = [
            'expired_at' => time() + $this->cacheLifeTime,
            'data' => $result
        ];

        $this->cache->write($cacheName, json_encode($cacheData));

        return $result;
    }

    protected function determineCacheName(array $properties)
    {
        return 'analytics.'.md5(serialize($properties));
    }
}
