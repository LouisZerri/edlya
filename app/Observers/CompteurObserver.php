<?php

namespace App\Observers;

use App\Models\Compteur;
use App\Services\PhotoCleanupService;

class CompteurObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Compteur $compteur): void
    {
        $this->photoCleanupService->cleanupCompteur($compteur);
    }
}
