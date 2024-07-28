<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\JobRepositoryContract;
use App\Contracts\ScrapperContract;
use App\Http\Requests\JobStoreRequest;
use App\Jobs\ScrapeJob;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    public function __construct(
        protected readonly JobRepositoryContract $jobRepository,
        protected ScrapperContract $scrapper,
    ) {}

    public function store(JobStoreRequest $request): JsonResponse
    {
        $data = Arr::get($request->validated(), 'data');
        $id = Str::ulid()->toBase32();

        $this->jobRepository->storeJob($id, $data);

        ScrapeJob::dispatch($this->jobRepository, $this->scrapper, $id, $data);

        return response()->json([
            'data' => [
                'id' => $id,
                'type' => 'jobs',
                'attributes' => [
                    'job_details' => $data,
                    'status' => JobStatusEnum::PENDING->value,
                ],
            ],
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $data = $this->jobRepository->getJob($id);

        if (! $data) {
            return response()->json([
                'message' => "Job was not found with id $id",
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
                'id' => $id,
                'type' => 'jobs',
                'attributes' => [
                    'job_details' => json_decode(Arr::get($data, 'data'), true),
                    'status' => Arr::get($data, 'status'),
                    'created_at' => Arr::get($data, 'created_at'),
                    'updated_at' => Arr::get($data, 'updated_at'),
                ],
            ],
        ], Response::HTTP_OK);
    }

    public function delete(string $id): JsonResponse
    {
        $jobDeleted = $this->jobRepository->deleteJob($id);

        $message = '';
        $statusCode = Response::HTTP_NO_CONTENT;

        if (! $jobDeleted) {
            $message = "Job was not found with id $id";
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}
