<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AideController extends Controller
{
    /**
     * Améliorer une observation avec l'IA
     */
    public function ameliorerObservation(Request $request): JsonResponse
    {
        $request->validate([
            'element' => 'required|string|max:100',
            'etat' => 'required|string|max:50',
            'observation' => 'nullable|string|max:500',
            'degradations' => 'nullable|array',
        ]);

        $apiKey = config('anthropic.api_key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API non configurée',
            ], 500);
        }

        $element = $request->element;
        $etat = $request->etat;
        $observation = $request->observation ?? '';
        $degradations = $request->degradations ?? [];

        $etatLabels = [
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon',
            'bon' => 'Bon',
            'usage' => 'Usagé',
            'mauvais' => 'Mauvais',
            'hors_service' => 'Hors service',
        ];

        $prompt = "Tu es un expert en états des lieux immobiliers. Rédige une observation professionnelle et concise pour un élément d'état des lieux.

            Élément : {$element}
            État : " . ($etatLabels[$etat] ?? $etat) . "
            " . (!empty($degradations) ? "Dégradations constatées : " . implode(', ', $degradations) : "") . "
            " . (!empty($observation) ? "Observation actuelle (à améliorer) : {$observation}" : "") . "

            Règles :
            - Maximum 100 caractères
            - Style professionnel et factuel
            - Ne pas utiliser de formules de politesse
            - Décrire l'état de manière objective
            - Si des dégradations sont mentionnées, les intégrer naturellement

            Réponds uniquement avec l'observation améliorée, sans guillemets ni explication.";

        try {

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 150,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $observation = trim($result['content'][0]['text'] ?? '');
                
                return response()->json([
                    'success' => true,
                    'observation' => $observation,
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Erreur API',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}