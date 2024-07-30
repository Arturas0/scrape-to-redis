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

        $chunkSize = 10;

        $selectorChunks = array_chunk($selectors, $chunkSize);

        foreach ($selectorChunks as $chunk) {
            foreach ($chunk as $selector) {
                $this->observer->scrappedData = [];
                $this->observer->setSelector($selector);

                Crawler::create()
                    ->setCrawlObserver($this->observer)
                    ->setMaximumDepth(0)
                    ->setTotalCrawlLimit(1)
                    ->ignoreRobots()
                    ->startCrawling($url);

                $selectorScrappedData[$selector] = $this->observer->scrappedData;
            }
        }

        return $selectorScrappedData;
    }
}
