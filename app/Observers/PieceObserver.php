<?php

namespace App\Observers;

use App\Models\Piece;
use App\Services\PhotoCleanupService;

class PieceObserver
{
    public function __construct(
        private PhotoCleanupService $photoCleanupService
    ) {}

    public function deleting(Piece $piece): void
    {
        $this->photoCleanupService->cleanupPiece($piece);
    }
}
