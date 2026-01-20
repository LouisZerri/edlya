<?php

namespace App\Observers;

use App\Models\Photo;
use App\Services\PhotoCleanupService;

class PhotoObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Photo $photo): void
    {
        $this->photoCleanupService->cleanupPhoto($photo);
    }
}
