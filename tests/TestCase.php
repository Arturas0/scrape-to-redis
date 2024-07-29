<?php

namespace Tests;

use App\Support\Enums\JobStatusEnum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Redis::flushdb();
    }

    public function createJob(?string $jobId = null, ?array $jobData = []): array
    {
        $id = $jobId ?? Str::ulid()->toBase32();
        $data = $jobData ?? [
            [
                'url' => 'https://example.com',
                'selectors' => ['h1'],
            ],
        ];

        Redis::hmset("job:$id", [
            'data' => json_encode($data),
            'status' => JobStatusEnum::PENDING->value,
        ]);

        return [
            'id' => $id,
            'data' => $data,
        ];
    }
}
