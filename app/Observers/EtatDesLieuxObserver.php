<?php

namespace App\Observers;

use App\Models\EtatDesLieux;
use App\Services\PhotoCleanupService;

class EtatDesLieuxObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(EtatDesLieux $etatDesLieux): void
    {
        $this->photoCleanupService->cleanupEtatDesLieux($etatDesLieux);
    }
}
