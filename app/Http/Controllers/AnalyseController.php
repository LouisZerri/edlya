<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyseDegradationRequest;
use App\Http\Requests\AnalysePhotoRequest;
use App\Http\Requests\AnalyseUploadRequest;
use App\Http\Requests\AppliquerElementsRequest;
use App\Models\Piece;
use App\Models\Element;
use App\Models\Photo;
use App\Services\AnthropicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function __construct(
        private AnthropicService $anthropicService
    ) {}

    public function uploadPhoto(AnalyseUploadRequest $request): JsonResponse
    {
        $piece = Piece::findOrFail($request->validated('piece_id'));

        $this->authorize('update', $piece);

        $path = $request->file('photo')->store('analyse-temp', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function analyserPhoto(AnalysePhotoRequest $request): JsonResponse
    {
        $piece = Piece::findOrFail($request->validated('piece_id'));

        $this->authorize('update', $piece);

        $elements = $this->anthropicService->analyserPiece($request->validated('photo_path'));

        if ($elements === null) {
            return response()->json([
                'error' => 'Impossible d\'analyser l\'image. Veuillez réessayer.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'elements' => $elements,
        ]);
    }

    public function appliquerElements(AppliquerElementsRequest $request): JsonResponse
    {
        $piece = Piece::findOrFail($request->validated('piece_id'));

        $this->authorize('update', $piece);

        foreach ($request->validated('elements') as $elementData) {
            $piece->elements()->create([
                'type' => $elementData['type'],
                'nom' => $elementData['nom'],
                'etat' => $elementData['etat'],
                'observations' => $elementData['observations'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->validated('elements')) . ' élément(s) ajouté(s).',
        ]);
    }

    public function analyserDegradation(AnalyseDegradationRequest $request): JsonResponse
    {
        $element = Element::with('piece.etatDesLieux')->findOrFail($request->validated('element_id'));
        $photo = Photo::findOrFail($request->validated('photo_id'));

        $this->authorize('update', $element);

        $result = $this->anthropicService->analyserDegradations(
            $photo->chemin,
            $element->nom,
            $element->type,
            $request->validated('etat_entree'),
            $element->etat,
            $request->validated('observations', '')
        );

        if ($result === null) {
            return response()->json([
                'error' => 'Impossible d\'analyser les dégradations. Veuillez réessayer.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'analyse' => $result,
        ]);
    }

    public function analyserDegradationFromPath(Request $request): JsonResponse
    {
        $request->validate([
            'photo_path' => ['required', 'string'],
            'element_nom' => ['required', 'string'],
            'element_type' => ['required', 'string'],
            'etat_entree' => ['required', 'string'],
            'etat_sortie' => ['required', 'string'],
            'observations' => ['nullable', 'string'],
        ]);

        $result = $this->anthropicService->analyserDegradations(
            $request->photo_path,
            $request->element_nom,
            $request->element_type,
            $request->etat_entree,
            $request->etat_sortie,
            $request->observations ?? ''
        );

        if ($result === null) {
            return response()->json([
                'error' => 'Impossible d\'analyser les dégradations. Veuillez réessayer.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'analyse' => $result,
        ]);
    }
}