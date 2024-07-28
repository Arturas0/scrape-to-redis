<?php

declare(strict_types=1);

namespace App\Contracts;

interface ScrapperContract
{
    public function scrape(string $jobId, string $url, array $selectors): ?array;
}
