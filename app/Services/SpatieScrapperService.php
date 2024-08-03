<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ScrapperContract;
use App\Spiders\JobSpider;
use Spatie\Crawler\Crawler;

class SpatieScrapperService implements ScrapperContract
{
    public function __construct(protected JobSpider $spider)
    {
    }

    public function scrape(string $jobId, string $url, array $selectors): ?array
    {
        $selectorScrappedData = [];
        $this->spider->scrappedData = [];

        $chunkSize = 10;

        $selectorChunks = array_chunk($selectors, $chunkSize);

        foreach ($selectorChunks as $chunk) {
            foreach ($chunk as $selector) {
                $this->spider->scrappedData = [];
                $this->spider->setSelector($selector);

                Crawler::create()
                    ->setCrawlObserver($this->spider)
                    ->setMaximumDepth(0)
                    ->setTotalCrawlLimit(1)
                    ->ignoreRobots()
                    ->startCrawling($url);

                $selectorScrappedData[$selector] = $this->spider->scrappedData;
            }
        }

        return $selectorScrappedData;
    }
}
