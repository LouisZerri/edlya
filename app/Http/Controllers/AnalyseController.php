<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use App\Models\Element;
use App\Models\Photo;
use App\Services\AnthropicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyseController extends Controller
{
    public function __construct(
        private AnthropicService $anthropicService
    ) {}

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
            'piece_id' => ['required', 'exists:pieces,id'],
        ]);

        $piece = Piece::findOrFail($request->piece_id);

        if ($piece->etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $path = $request->file('photo')->store('analyse-temp', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function analyserPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo_path' => ['required', 'string'],
            'piece_id' => ['required', 'exists:pieces,id'],
        ]);

        $piece = Piece::findOrFail($request->piece_id);

        if ($piece->etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $elements = $this->anthropicService->analyserPiece($request->photo_path);

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

    public function appliquerElements(Request $request): JsonResponse
    {
        $request->validate([
            'piece_id' => ['required', 'exists:pieces,id'],
            'elements' => ['required', 'array'],
            'elements.*.type' => ['required', 'string'],
            'elements.*.nom' => ['required', 'string'],
            'elements.*.etat' => ['required', 'string'],
            'elements.*.observations' => ['nullable', 'string'],
        ]);

        $piece = Piece::findOrFail($request->piece_id);

        if ($piece->etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        foreach ($request->elements as $elementData) {
            $piece->elements()->create([
                'type' => $elementData['type'],
                'nom' => $elementData['nom'],
                'etat' => $elementData['etat'],
                'observations' => $elementData['observations'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->elements) . ' élément(s) ajouté(s).',
        ]);
    }

    public function analyserDegradation(Request $request): JsonResponse
    {
        $request->validate([
            'element_id' => ['required', 'exists:elements,id'],
            'photo_id' => ['required', 'exists:photos,id'],
            'etat_entree' => ['required', 'string'],
            'observations' => ['nullable', 'string'],
        ]);

        $element = Element::with('piece.etatDesLieux')->findOrFail($request->element_id);
        $photo = Photo::findOrFail($request->photo_id);

        if ($element->piece->etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $result = $this->anthropicService->analyserDegradations(
            $photo->chemin,
            $element->nom,
            $element->type,
            $request->etat_entree,
            $element->etat,
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