<?php

declare(strict_types=1);

namespace App\Support\Enums;

enum JobStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
}
