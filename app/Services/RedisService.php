<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\JobRepositoryContract;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Support\Facades\Redis;

class RedisService implements JobRepositoryContract
{
    public function storeJob(string $jobId, array $data): void
    {
        Redis::hmset("job:$jobId", [
            'data' => json_encode($data),
            'status' => JobStatusEnum::PENDING->value,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function addScrappedDataToJob(string $jobId, string $url, array $scrapedData): void
    {
        $jobData = Redis::hget("job:$jobId", 'data');
        $data = json_decode($jobData, true);
        $chunkSize = 10;

        $dataChunks = array_chunk($data, $chunkSize);

        foreach ($dataChunks as &$chunk) {
            foreach ($chunk as &$item) {
                if ($item['url'] === $url) {
                    if (! isset($item['scrapedData'])) {
                        $item['scrapedData'] = [];
                    }

                    $item['scrapedData'] = $scrapedData;
                }
            }
        }

        $updatedData = array_merge(...$dataChunks);

        Redis::hset("job:$jobId", 'data', json_encode($updatedData));
    }

    public function getJob(string $jobId): array
    {
        return Redis::hgetAll("job:$jobId");
    }

    public function deleteJob(string $jobId): bool
    {
        return (bool) Redis::del("job:$jobId");
    }

    public function changeJobStatus(string $jobId, JobStatusEnum $status): bool
    {
        if (Redis::exists("job:$jobId")) {
            Redis::hset("job:$jobId", 'status', $status->value);

            return true;
        }

        return false;
    }

    public function changeUpdatedAt(string $jobId): void
    {
        if (Redis::exists("job:$jobId")) {
            Redis::hset("job:$jobId", 'updated_at', now()->toDateTimeString());
        }
    }
}
