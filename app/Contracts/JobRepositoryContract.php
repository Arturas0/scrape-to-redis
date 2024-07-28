<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Support\Enums\JobStatusEnum;

interface JobRepositoryContract
{
    public function storeJob(string $jobId, array $data);

    public function getJob(string $jobId): array;

    public function deleteJob(string $jobId): bool;

    public function addScrappedDataToJob(string $jobId, string $url, array $scrapedData): void;

    public function changeJobStatus(string $jobId, JobStatusEnum $status): bool;

    public function changeUpdatedAt(string $jobId): void;
}
