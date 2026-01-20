<?php

namespace App\Observers;

use App\Models\Element;
use App\Services\PhotoCleanupService;

class ElementObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Element $element): void
    {
        $this->photoCleanupService->cleanupElement($element);
    }
}
