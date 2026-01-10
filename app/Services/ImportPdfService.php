<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportPdfService
{
    private ?string $apiKey = null;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('anthropic.api_key');
        $this->model = config('anthropic.model', 'claude-sonnet-4-20250514');
    }

    /**
     * Analyse un PDF d'état des lieux et extrait les données structurées
     */
    public function analyserPdf(string $pdfPath): ?array
{
    if (!$this->apiKey) {
        Log::error('Clé API Anthropic manquante');
        return null;
    }

    $images = $this->convertPdfToImages($pdfPath);

    if (empty($images)) {
        throw new \Exception('Impossible de convertir le PDF en images.');
    }

    $prompt = $this->buildExtractionPrompt();

    $content = [];

    foreach ($images as $imagePath) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $content[] = [
            'type' => 'image',
            'source' => [
                'type' => 'base64',
                'media_type' => 'image/png',
                'data' => $imageData,
            ],
        ];
    }

    $content[] = [
        'type' => 'text',
        'text' => $prompt,
    ];

    /** @var \Illuminate\Http\Client\Response $response */
    $response = Http::withHeaders([
        'x-api-key' => $this->apiKey,
        'anthropic-version' => '2023-06-01',
        'Content-Type' => 'application/json',
    ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
        'model' => $this->model,
        'max_tokens' => 8000,
        'messages' => [
            [
                'role' => 'user',
                'content' => $content,
            ],
        ],
    ]);

    // Nettoyer les images temporaires
    foreach ($images as $imagePath) {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    if (!$response->successful()) {
        throw new \Exception('Erreur API Anthropic: ' . $response->body());
    }

    $result = $response->json();
    $textContent = $result['content'][0]['text'] ?? '';

    // DEBUG : voir ce que l'IA retourne
    Log::info('Import PDF - Réponse IA', ['response' => $textContent]);

    return $this->parseJsonResponse($textContent);
}

    private function convertPdfToImages(string $pdfPath): array
    {
        $tempDir = storage_path('app/temp/pdf_' . uniqid());

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Vérifier que le fichier existe
        if (!file_exists($pdfPath)) {
            throw new \Exception('Fichier PDF introuvable: ' . $pdfPath);
        }

        $outputPrefix = $tempDir . '/page';
        $command = sprintf(
            'pdftoppm -png -r 150 %s %s 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($outputPrefix)
        );

        exec($command, $output, $returnCode);

        // Debug
        if ($returnCode !== 0) {

            // Essayer ImageMagick
            $command = sprintf(
                'convert -density 150 %s %s/page-%%03d.png 2>&1',
                escapeshellarg($pdfPath),
                escapeshellarg($tempDir)
            );
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                // Nettoyer le répertoire temporaire en cas d'erreur
            }
        }

        $files = glob($tempDir . '/*.png');
        sort($files);

        return array_slice($files, 0, 20);
    }

    private function buildExtractionPrompt(): string
    {
        return <<<'PROMPT'
            Tu es un expert en analyse de documents immobiliers. Analyse ce PDF d'état des lieux et extrait TOUTES les informations.

            Retourne UNIQUEMENT un objet JSON valide (sans markdown, sans ```json) avec cette structure :

            {
                "type": "entree" ou "sortie",
                "date_realisation": "YYYY-MM-DD",
                "logement": {
                    "nom": "Nom/Type du bien (ex: Appartement T3, Studio meublé, Maison)",
                    "adresse": "adresse complète avec code postal et ville",
                    "type_bien": "appartement" ou "maison" ou "studio",
                    "surface": nombre ou null,
                    "nombre_pieces": nombre ou null
                },
                "locataire": {
                    "nom": "nom complet",
                    "email": null,
                    "telephone": null
                },
                "compteurs": {
                    "electricite": {
                        "numero": "numéro" ou null,
                        "releve": "valeur" ou null
                    },
                    "eau": {
                        "numero": "numéro" ou null,
                        "releve": "valeur" ou null
                    }
                },
                "pieces": [
                    {
                        "nom": "Nom de la pièce",
                        "elements": [
                            {
                                "nom": "Nom élément",
                                "type": "sol" ou "mur" ou "plafond" ou "menuiserie" ou "electricite" ou "plomberie" ou "chauffage" ou "mobilier" ou "electromenager" ou "autre",
                                "quantite": nombre ou 1,
                                "etat": "neuf" ou "bon_etat" ou "etat_moyen" ou "mauvais_etat",
                                "observations": "observations" ou null
                            }
                        ]
                    }
                ],
                "cles": [
                    {
                        "type": "Type de clé",
                        "nombre": nombre
                    }
                ],
                "observations_generales": "texte" ou null
            }

            IMPORTANT:
            - Pour "logement.nom", utilise le type de bien indiqué (ex: "Appartement loué meublé", "Studio", "Maison T4"), PAS l'adresse
            - Pour "etat", convertis: "Neuf" → "neuf", "Bon état" → "bon_etat", "État moyen" → "etat_moyen", "Mauvais état" → "mauvais_etat"
            - Extrait TOUTES les pièces et TOUS les éléments du document
            - Si une information n'est pas présente, utilise null
        PROMPT;
    }

    private function parseJsonResponse(string $text): array
    {
        $text = trim($text);

        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $text, $matches)) {
            $text = $matches[1];
        }

        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Réponse JSON invalide: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
