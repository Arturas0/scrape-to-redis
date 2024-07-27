<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\JobStoreRequest;
use App\Interfaces\DataManagementInterface;
use App\Support\Enums\JobStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    public function __construct(private readonly DataManagementInterface $dataManager) {}

    public function store(JobStoreRequest $request): JsonResponse
    {
        $data = Arr::get($request->validated(), 'data');
        $jobId = Str::ulid()->toBase32();

        $this->dataManager->storeJob($jobId, $data);

        //trigger scrape job

        return response()->json([
            'data' => [
                'id' => $jobId,
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
        $data = $this->dataManager->getJob($id);

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
                    'job_details' => Arr::get($data, 'data'),
                    'status' => Arr::get($data, 'status'),
                ],
            ],
        ], Response::HTTP_OK);
    }

    public function delete(string $id): JsonResponse
    {
        $jobDeleted = $this->dataManager->deleteJob($id);

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
