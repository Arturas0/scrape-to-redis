<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ScrapperContract;
use App\Observers\SpatieCrawlerObserver;
use Spatie\Crawler\Crawler;

class SpatieScrapperService implements ScrapperContract
{
    public function __construct(protected SpatieCrawlerObserver $observer,
    ) {}

    public function scrape(string $jobId, string $url, array $selectors): ?array
    {
        $selectorScrappedData = [];
        $this->observer->scrappedData = [];

        foreach ($selectors as $selector) {
            $this->observer->setSelector($selector);

            Crawler::create()
                ->setCrawlObserver($this->observer)
                ->setMaximumDepth(0)
                ->setTotalCrawlLimit(1)
                ->ignoreRobots()
                ->startCrawling($url);

            $selectorScrappedData[$selector] = $this->observer->scrappedData;
        }

        return $selectorScrappedData;
    }
}
