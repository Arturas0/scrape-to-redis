<?php

namespace Tests\Unit;

use App\Contracts\JobRepositoryContract;
use App\Contracts\ScrapperContract;
use App\Jobs\ScrapeJob;
use App\Support\Enums\JobStatusEnum;
use Mockery;
use Tests\TestCase;

class ScrapeJobTest extends TestCase
{
    public function test_scrape_job_return_correct_data(): void
    {
        $jobRepositoryMock = Mockery::mock(JobRepositoryContract::class);
        $scrapperMock = Mockery::mock(ScrapperContract::class);

        $jobId = 'job123';
        $urlsWithSelectors = [
            [
                'url' => 'http://example.com',
                'selectors' => ['.some-css-class'],
            ],
        ];

        $scrappedData = [
            'http://example.com' => [
                '.some-css-class' => 'Scrapped Content',
            ],
        ];

        $scrapperMock->shouldReceive('scrape')
            ->once()
            ->with($jobId, 'http://example.com', ['.some-css-class'])
            ->andReturn($scrappedData);

        $jobRepositoryMock->shouldReceive('addScrappedDataToJob')
            ->once()
            ->with($jobId, 'http://example.com', $scrappedData);

        $jobRepositoryMock->shouldReceive('changeJobStatus')
            ->once()
            ->with($jobId, JobStatusEnum::COMPLETED);

        $jobRepositoryMock->shouldReceive('changeUpdatedAt')
            ->once()
            ->with($jobId);

        $job = new ScrapeJob($jobRepositoryMock, $scrapperMock, $jobId, $urlsWithSelectors);
        $job->handle();
    }
}
