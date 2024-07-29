<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'id' => Arr::get($this, 'id'),
                'type' => 'jobs',
                'attributes' => [
                    'job_details' => json_decode(Arr::get($this, 'resource.data'), true),
                    'status' => Arr::get($this, 'resource.status'),
                    'created_at' => Arr::get($this, 'resource.created_at'),
                    'updated_at' => Arr::get($this, 'resource.updated_at'),
                ],
            ],
        ];
    }
}
