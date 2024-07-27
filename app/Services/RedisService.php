<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\DataManagementInterface;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Support\Facades\Redis;

class RedisService implements DataManagementInterface
{
    public function storeJob(string $jobId, array $data): void
    {
        Redis::hmset("job:$jobId", [
            'data' => json_encode($data),
            'status' => JobStatusEnum::PENDING->value,
        ]);
    }

    public function getJob(string $jobId): array
    {
        return Redis::hgetAll("job:$jobId");
    }
}
