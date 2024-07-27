<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => ['array', 'required'],
            'data.*.url' => ['required', 'string', 'distinct'],
            'data.*.selectors' => ['required', 'array'],
        ];
    }
}
