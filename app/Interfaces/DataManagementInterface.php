<?php

declare(strict_types=1);

namespace App\Interfaces;

interface DataManagementInterface
{
    public function storeJob(string $jobId, array $data);
}
