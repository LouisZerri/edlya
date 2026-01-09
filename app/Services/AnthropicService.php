<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnthropicService
{
    private string $apiKey;
    private string $model;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('anthropic.api_key', '');
        $this->model = config('anthropic.model', 'claude-sonnet-4-20250514');
        $this->maxTokens = config('anthropic.max_tokens', 1024);
    }

    public function analyserPiece(string $imagePath): ?array
    {
        if (!$this->apiKey) {
            Log::error('Clé API Anthropic manquante');
            return null;
        }

        $imageData = $this->encodeImage($imagePath);
        if (!$imageData) {
            return null;
        }

        $prompt = $this->buildPrompt();

        try {

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => $imageData['media_type'],
                                    'data' => $imageData['data'],
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Erreur API Anthropic', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $this->parseResponse($response->json());

        } catch (\Exception $e) {
            Log::error('Exception API Anthropic', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function analyserDegradations(string $imagePath, string $elementNom, string $elementType, string $etatEntree, string $etatSortie, string $observations = ''): ?array
    {
        if (!$this->apiKey) {
            Log::error('Clé API Anthropic manquante');
            return null;
        }

        $imageData = $this->encodeImage($imagePath);
        if (!$imageData) {
            return null;
        }

        $prompt = $this->buildDegradationPrompt($elementNom, $elementType, $etatEntree, $etatSortie, $observations);

        try {

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => $imageData['media_type'],
                                    'data' => $imageData['data'],
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Erreur API Anthropic (dégradations)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $this->parseDegradationResponse($response->json());

        } catch (\Exception $e) {
            Log::error('Exception API Anthropic (dégradations)', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function encodeImage(string $path): ?array
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            Log::error('Image non trouvée', ['path' => $fullPath]);
            return null;
        }

        $imageData = file_get_contents($fullPath);
        $mimeType = mime_content_type($fullPath);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedTypes)) {
            Log::error('Type d\'image non supporté', ['mime' => $mimeType]);
            return null;
        }

        return [
            'data' => base64_encode($imageData),
            'media_type' => $mimeType,
        ];
    }

    private function buildPrompt(): string
    {
        return <<<PROMPT
            Tu es un expert en états des lieux immobiliers. Analyse cette photo d'une pièce et identifie tous les éléments visibles qui doivent être documentés dans un état des lieux.

            Pour chaque élément détecté, indique :
            - type : sol, mur, plafond, menuiserie, electricite, plomberie, chauffage, equipement
            - nom : description courte de l'élément
            - etat : neuf, tres_bon, bon, usage, mauvais, hors_service
            - observations : détails ou anomalies visibles

            Réponds UNIQUEMENT avec un objet JSON valide au format suivant, sans aucun texte avant ou après :
            {
                "elements": [
                    {
                        "type": "sol",
                        "nom": "Parquet chêne",
                        "etat": "bon",
                        "observations": "Quelques rayures légères"
                    }
                ]
            }
        PROMPT;
    }

    private function buildDegradationPrompt(string $elementNom, string $elementType, string $etatEntree, string $etatSortie, string $observations): string
    {
        $etatsLibelles = [
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon',
            'bon' => 'Bon',
            'usage' => 'Usagé',
            'mauvais' => 'Mauvais',
            'hors_service' => 'Hors service',
        ];

        $etatEntreeLibelle = $etatsLibelles[$etatEntree] ?? $etatEntree;
        $etatSortieLibelle = $etatsLibelles[$etatSortie] ?? $etatSortie;

        return <<<PROMPT
            Tu es un expert en états des lieux immobiliers et en estimation de réparations.

            Contexte :
            - Élément : {$elementNom} ({$elementType})
            - État à l'entrée : {$etatEntreeLibelle}
            - État à la sortie : {$etatSortieLibelle}
            - Observations constatées : {$observations}

            Analyse cette photo et identifie précisément les dégradations visibles. Pour chaque dégradation, propose une réparation avec estimation de coût.

            Réponds UNIQUEMENT avec un objet JSON valide au format suivant, sans aucun texte avant ou après :
            {
                "degradations": [
                    {
                        "description": "Description précise de la dégradation",
                        "gravite": "legere|moyenne|importante",
                        "reparation": "Description de la réparation nécessaire",
                        "unite": "m2|ml|unite|forfait",
                        "quantite_estimee": 1,
                        "prix_unitaire_estime": 25.00
                    }
                ],
                "commentaire_general": "Résumé général de l'état et des réparations nécessaires"
            }

            Base tes estimations de prix sur les tarifs moyens en France :
            - Peinture mur/plafond : 18-25€/m²
            - Rebouchage trou : 8-25€/unité
            - Ponçage parquet : 35€/m²
            - Remplacement moquette : 25€/m²
            - Nettoyage professionnel : 5-10€/m²
            - Remplacement prise/interrupteur : 40-50€/unité
            - Plomberie (robinetterie) : 35-100€/unité
        PROMPT;
    }

    private function parseResponse(?array $response): ?array
    {
        if (!$response || !isset($response['content'][0]['text'])) {
            return null;
        }

        $text = $response['content'][0]['text'];
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        try {
            $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
            return $data['elements'] ?? null;
        } catch (\JsonException $e) {
            Log::error('Erreur parsing JSON Anthropic', [
                'error' => $e->getMessage(),
                'text' => $text,
            ]);
            return null;
        }
    }

    private function parseDegradationResponse(?array $response): ?array
    {
        if (!$response || !isset($response['content'][0]['text'])) {
            return null;
        }

        $text = $response['content'][0]['text'];
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        try {
            $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
            return $data;
        } catch (\JsonException $e) {
            Log::error('Erreur parsing JSON Anthropic (dégradations)', [
                'error' => $e->getMessage(),
                'text' => $text,
            ]);
            return null;
        }
    }
}