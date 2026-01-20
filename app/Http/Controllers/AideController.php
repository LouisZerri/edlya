<?php

namespace App\Http\Controllers;

use App\Http\Requests\AmeliorerObservationRequest;
use App\Services\AnthropicService;
use Illuminate\Http\JsonResponse;

class AideController extends Controller
{
    public function __construct(
        private AnthropicService $anthropicService
    ) {}

    /**
     * Améliorer une observation avec l'IA
     */
    public function ameliorerObservation(AmeliorerObservationRequest $request): JsonResponse
    {
        $observation = $this->anthropicService->ameliorerObservation(
            $request->validated('element'),
            $request->validated('etat'),
            $request->validated('observation'),
            $request->validated('degradations', [])
        );

        if ($observation === null) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'amélioration de l\'observation',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'observation' => $observation,
        ]);
    }
}