<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\JobRepositoryContract;
use App\Contracts\ScrapperContract;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;

class ScrapeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected JobRepositoryContract $jobRepository,
        protected readonly ScrapperContract $scrapper,
        protected readonly string $jobId,
        protected readonly array $urlsWithSelectors,
    ) {}

    public function handle()
    {
        foreach ($this->urlsWithSelectors as $urlWithSelectors) {
            $url = Arr::get($urlWithSelectors, 'url');
            $selectors = Arr::get($urlWithSelectors, 'selectors');

            $scrappedData = $this->scrapper->scrape($this->jobId, $url, $selectors);

            $this->jobRepository->addScrappedDataToJob($this->jobId, $url, $scrappedData);
            $this->jobRepository->changeJobStatus($this->jobId, JobStatusEnum::COMPLETED);
            $this->jobRepository->changeUpdatedAt($this->jobId);
        }
    }
}
