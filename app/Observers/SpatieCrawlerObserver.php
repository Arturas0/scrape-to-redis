<?php

declare(strict_types=1);

namespace App\Observers;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class SpatieCrawlerObserver extends CrawlObserver
{
    public function __construct(
        protected string $selector = '',
        public array $scrappedData = [],
    ) {}

    public function willCrawl(UriInterface $url, ?string $linkText): void {}

    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {
        $crawler = new DomCrawler($response->getBody()->__toString());

        if ($crawler->filter($this->selector)->count() > 0 && $crawler->filter("{$this->selector} > li")->count() > 0) {
            $crawler->filter("{$this->selector} > li")->each(function (DomCrawler $node) {
                $this->scrappedData[] = $node->text();
            });
        } else {
            $crawler->filter($this->selector)->each(function (DomCrawler $node) {
                $this->scrappedData[] = $node->text();
            });
        }
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {}

    public function finishedCrawling(): void {}

    public function setSelector(string $selector): void
    {
        $this->selector = $selector;
    }
}
