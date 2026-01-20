<?php

namespace App\Observers;

use App\Models\Cle;
use App\Services\PhotoCleanupService;

class CleObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Cle $cle): void
    {
        $this->photoCleanupService->cleanupCle($cle);
    }
}
