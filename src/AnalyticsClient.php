<?php

namespace AgungMaxsol\Analytics;

use Google_Service_Analytics;

class AnalyticsClient
{
    protected $service;

    public function __construct(Google_Service_Analytics $service)
    {
        $this->service = $service;
    }

    public function getAnalyticsService()
    {
        return $this->service;
    }

    public function performQuery(
        string $viewId,
        \DateTime $startDate,
        \DateTime $endDate,
        string $metrics,
        array $others = []
    ) {
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

        return $result;
    }
}
