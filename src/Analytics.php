<?php

namespace AgungMaxsol\Analytics;

class Analytics
{
    protected $client;
    protected $viewId;

    public function __construct(AnalyticsClient $client, string $viewId)
    {
        $this->client = $client;
        $this->viewId = $viewId;
    }

    public function setViewId(string $viewId)
    {
        $this->viewId = $viewId;

        return $this;
    }

    public function getAnalyticsService()
    {
        return $this->client->getAnalyticsService();
    }

    public function performQuery($period, string $metrics, array $others = [])
    {
        return $this->client->performQuery(
            $this->viewId,
            $period->startDate,
            $period->endDate,
            $metrics,
            $others
        );
    }

    public function fetchVisitorsAndPageViews($period)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date,ga:pageTitle']
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $data[] = [
                    'date' => date('Y-m-d', strtotime($row[0])),
                    'pageTitle' => $row[1],
                    'visitors' => (int) $row[2],
                    'pageViews' => (int) $row[3]
                ];
            }
        }

        return $data;
    }

    public function fetchTotalVisitorsAndPageViews($period)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date']
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $data[] = [
                    'date' => date('Y-m-d', strtotime($row[0])),
                    'visitors' => (int) $row[1],
                    'pageViews' => (int) $row[2]
                ];
            }
        }

        return $data;
    }

    public function fetchMostVisitedPages($period, int $maxResults = 20)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:pagePath,ga:pageTitle',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults
            ]
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $data[] = [
                    'url' => $row[0],
                    'pageTitle' => $row[1],
                    'pageViews' => (int) $row[2]
                ];
            }
        }

        return $data;
    }

    public function fetchTopReferrers($period, int $maxResults = 20)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:fullReferrer',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults
            ]
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $data[] = [
                    'url' => $row[0],
                    'pageViews' => (int) $row[1]
                ];
            }
        }

        return $data;
    }

    public function fetchUserTypes($period)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            ['dimensions' => 'ga:userType']
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $data[] = [
                    'type' => $row[0],
                    'sessions' => (int) $row[1]
                ];
            }
        }

        return $data;
    }

    public function fetchTopBrowsers($period, int $maxResults = 10)
    {
        $data = [];
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:browser',
                'sort' => '-ga:sessions'
            ]
        );

        if (isset($response['rows'])) {
            foreach ($response['rows'] as $key => $row) {
                $data[] = [
                    'browser' => $row[0],
                    'sessions' => (int) $row[1]
                ];
            }
        }

        $count = count($data);

        if ($count <= $maxResults || $maxResults === 0) {
            return $data;
        }

        return $this->summarizeTopBrowsers($data, $count, $maxResults);
    }

    protected function summarizeTopBrowsers($data, int $count, int $maxResults)
    {
        $sessions = 0;

        for ($i = ($count - $maxResults); $i < $count; $i++) {
            $sessions += $data[$i]['sessions'];
            unset($data[$i]);
        }

        $data[] = [
            'browser' => 'Others',
            'sessions' => $sessions
        ];

        return array_values($data);
    }
}
