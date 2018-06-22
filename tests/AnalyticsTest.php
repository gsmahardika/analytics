<?php

namespace AgungMaxsol\Analytics\Tests;

use AgungMaxsol\Analytics\Analytics;
use AgungMaxsol\Analytics\AnalyticsClient;
use AgungMaxsol\Analytics\AnalyticsClientFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class AnalyticsTest extends TestCase
{
    protected $viewId;
    protected $client;
    protected $analytics;
    protected $period;

    public function setUp()
    {
        $period = new \stdClass;
        $period->startDate = new \DateTime('2017-04-01');
        $period->endDate = new \DateTime('2017-04-30');

        $this->viewId = '1234567';
        $this->client = Mockery::mock(AnalyticsClient::class);
        $this->analytics = new Analytics($this->client, $this->viewId);
        $this->period = $period;
    }

    public function testFetchVisitorAndPageViews()
    {
        $expectedArguments = [
            $this->viewId,
            $this->period->startDate,
            $this->period->endDate,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date,ga:pageTitle']
        ];

        $this->client
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [['20160101', 'pageTitle', '1', '2']]
            ]);

        $response = $this->analytics->fetchVisitorsAndPageViews($this->period);

        $this->assertEquals('2016-01-01', $response[0]['date']);
        $this->assertEquals('pageTitle', $response[0]['pageTitle']);
        $this->assertEquals(1, $response[0]['visitors']);
        $this->assertEquals(2, $response[0]['pageViews']);
    }

    public function testFetchTotalVisitorAndPageViews()
    {
        $expectedArguments = [
            $this->viewId,
            $this->period->startDate,
            $this->period->endDate,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date'],
        ];

        $this->client
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [['20160101', '1', '2']]
            ]);

        $response = $this->analytics->fetchTotalVisitorsAndPageViews($this->period);

        $this->assertEquals('2016-01-01', $response[0]['date']);
        $this->assertEquals(1, $response[0]['visitors']);
        $this->assertEquals(2, $response[0]['pageViews']);
    }

    public function testFetchMostVisitedPages()
    {
        $maxResults = 10;
        $expectedArguments = [
            $this->viewId,
            $this->period->startDate,
            $this->period->endDate,
            'ga:pageviews',
            [
                'dimensions' => 'ga:pagePath,ga:pageTitle',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults
            ]
        ];

        $this->client
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [['https://test.com', 'Page title', '123']]
            ]);

        $response = $this->analytics->fetchMostVisitedPages($this->period, $maxResults);

        $this->assertEquals('https://test.com', $response[0]['url']);
        $this->assertEquals('Page title', $response[0]['pageTitle']);
        $this->assertEquals(123, $response[0]['pageViews']);
    }

    public function testFetchTopReferrers()
    {
        $maxResults = 10;
        $expectedArguments = [
            $this->viewId,
            $this->period->startDate,
            $this->period->endDate,
            'ga:pageviews',
            [
                'dimensions' => 'ga:fullReferrer',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults
            ]
        ];

        $this->client
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [['https://referrer.com', '123']]
            ]);

        $response = $this->analytics->fetchTopReferrers($this->period, $maxResults);

        $this->assertEquals('https://referrer.com', $response[0]['url']);
        $this->assertEquals(123, $response[0]['pageViews']);
    }

    public function testFetchTopBrowsers()
    {
        $expectedArguments = [
            $this->viewId,
            $this->period->startDate,
            $this->period->endDate,
            'ga:sessions',
            [
                'dimensions' => 'ga:browser',
                'sort' => '-ga:sessions'
            ]
        ];

        $this->client
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [
                    ['Browser 1', '100'],
                    ['Browser 2', '90'],
                    ['Browser 3', '30'],
                    ['Browser 4', '20'],
                    ['Browser 1', '10']
                ],
            ]);

        $response = $this->analytics->fetchTopBrowsers($this->period, 3);

        $this->assertEquals([
            ['browser' => 'Browser 1', 'sessions' => 100],
            ['browser' => 'Browser 2', 'sessions' => 90],
            ['browser' => 'Others', 'sessions' => 60]
        ], $response);
    }
}
