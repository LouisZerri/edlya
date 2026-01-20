<?php

namespace App\Observers;

use App\Models\Logement;
use App\Services\PhotoCleanupService;

class LogementObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Logement $logement): void
    {
        $this->photoCleanupService->cleanupLogement($logement);
    }
}
