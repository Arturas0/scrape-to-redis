<?php

namespace Tests\Feature;

use App\Jobs\ScrapeJob;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class JobControllerTest extends TestCase
{
    public function test_new_scrapping_job_was_created_in_redis(): void
    {
        $payload = [
            'data' => [
                [
                    'url' => 'https://example.com',
                    'selectors' => ['.some-css-class'],
                ],
            ],
        ];

        $response = $this->json('POST', '/api/jobs', $payload)
            ->assertStatus(Response::HTTP_CREATED);

        $jobId = Arr::get($response, 'data.id');
        $jobDataFromRedis = json_decode(
            Arr::get(Redis::hgetAll("job:$jobId"), 'data'), true,
        );

        $jobPendingStatus = JobStatusEnum::PENDING->value;

        $this->assertSame(
            [
                'status' => $jobPendingStatus,
                'data' => Arr::get($payload, 'data'),
            ],
            [
                'status' => $jobPendingStatus,
                'data' => $jobDataFromRedis,
            ]);
    }

    public function test_new_scrapping_job_was_pushed_to_queue_for_scrapping(): void
    {
        Queue::fake();

        $payload = [
            'data' => [
                [
                    'url' => 'https://example.com',
                    'selectors' => ['.some-css-class'],
                ],
            ],
        ];

        $this->json('POST', '/api/jobs', $payload);

        Queue::assertPushed(ScrapeJob::class);
    }
}