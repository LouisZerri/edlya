<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Convertir PDF en images pour l'analyse
        $images = $this->convertPdfToImages($pdfPath);

        if (empty($images)) {
            throw new \Exception('Impossible de convertir le PDF en images.');
        }

        // Extraire les photos du PDF
        $extractedPhotos = $this->extractPhotosFromPdf($pdfPath);

        $prompt = $this->buildExtractionPrompt(count($extractedPhotos));

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

        // Nettoyer les images de pages temporaires
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

        Log::info('Import PDF - Réponse IA', ['response' => $textContent]);

        $data = $this->parseJsonResponse($textContent);
        
        // Ajouter les photos extraites aux données
        $data['_extracted_photos'] = $extractedPhotos;

        return $data;
    }

    /**
     * Extraire les photos intégrées du PDF
     */
    private function extractPhotosFromPdf(string $pdfPath): array
    {
        $tempDir = storage_path('app/temp/photos_' . uniqid());
        
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Utiliser pdfimages pour extraire les images
        $command = sprintf(
            'pdfimages -png %s %s/photo 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($tempDir)
        );

        exec($command, $output, $returnCode);

        $photos = [];
        
        if ($returnCode === 0) {
            $files = glob($tempDir . '/*.png');
            sort($files);

            // Filtrer les petites images (logos, icônes) - garder seulement les photos
            foreach ($files as $file) {
                $imageInfo = getimagesize($file);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    
                    // Garder seulement les images assez grandes (probablement des photos)
                    // Exclure les très petites (icônes) et les très allongées (headers)
                    if ($width >= 100 && $height >= 100 && $width < 3000 && $height < 3000) {
                        $ratio = $width / $height;
                        // Ratio entre 0.5 et 2 (pas trop allongé)
                        if ($ratio >= 0.5 && $ratio <= 2) {
                            $photos[] = [
                                'path' => $file,
                                'width' => $width,
                                'height' => $height,
                            ];
                        } else {
                            unlink($file);
                        }
                    } else {
                        unlink($file);
                    }
                }
            }
        }

        return $photos;
    }

    private function convertPdfToImages(string $pdfPath): array
    {
        $tempDir = storage_path('app/temp/pdf_' . uniqid());

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

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

        if ($returnCode !== 0) {
            $command = sprintf(
                'convert -density 150 %s %s/page-%%03d.png 2>&1',
                escapeshellarg($pdfPath),
                escapeshellarg($tempDir)
            );
            exec($command, $output, $returnCode);
        }

        $files = glob($tempDir . '/*.png');
        sort($files);

        return array_slice($files, 0, 20);
    }

    private function buildExtractionPrompt(int $photoCount = 0): string
    {
        $photoInstruction = '';
        if ($photoCount > 0) {
            $photoInstruction = "
                PHOTOS IMPORTANTES:
                - {$photoCount} photos ont été extraites du PDF.
                - Dans 'photo_indices', indique les numéros des photos (1, 2, 3...) associées à chaque élément.
                - Cherche les mentions 'Photo 1', 'Photo 2', '(Photo 1)', 'cf. Photo 1' dans les observations.";
        }

        return <<<PROMPT
            Tu es un expert en analyse de documents immobiliers français. Analyse MINUTIEUSEMENT ce PDF d'état des lieux.

            RÈGLES D'EXTRACTION CRITIQUES :
            1. Lis CHAQUE PAGE du document attentivement
            2. Extrait TOUTES les observations, même les plus courtes ("RAS", "OK", "Bon état", etc.)
            3. Ne résume JAMAIS les observations - copie-les mot pour mot
            4. Si un élément a une observation, elle DOIT apparaître dans le JSON
            5. Cherche les observations dans les colonnes "Observations", "Remarques", "Commentaires", "Description"

            CONVERSION DES ÉTATS (très important) :
            - "Neuf", "Très bon état", "Excellent" → "neuf"
            - "Bon état", "Bon", "Correct", "RAS" → "bon_etat"  
            - "État moyen", "Usagé", "Usure normale", "Traces d'usure" → "etat_moyen"
            - "Mauvais état", "Mauvais", "Dégradé", "Hors service", "À remplacer" → "mauvais_etat"

            TYPES D'ÉLÉMENTS :
            - Sol : parquet, carrelage, moquette, lino, etc.
            - Mur : murs, peinture, papier peint, crépi, etc.
            - Plafond : plafond, faux plafond, etc.
            - Menuiserie : fenêtre, porte, volet, placard, etc.
            - Electricite : prises, interrupteurs, luminaires, tableau électrique, etc.
            - Plomberie : lavabo, douche, baignoire, WC, robinet, évier, etc.
            - Chauffage : radiateur, chaudière, convecteur, etc.
            - Equipement : plan de travail, hotte, plaques, four, etc.
            - Mobilier : lit, table, chaise, canapé, armoire, etc.
            - Electromenager : réfrigérateur, lave-linge, micro-ondes, etc.
            - Autre : tout ce qui ne rentre pas dans les catégories ci-dessus

            Retourne UNIQUEMENT un objet JSON valide (sans ```json, sans commentaires) :

            {
                "type": "entree" ou "sortie",
                "date_realisation": "YYYY-MM-DD",
                "logement": {
                    "nom": "Type du bien (Appartement T3, Studio meublé, Maison, etc.) - PAS l'adresse",
                    "adresse": "Adresse complète avec numéro, rue, code postal et ville",
                    "type_bien": "appartement" ou "maison" ou "studio",
                    "surface": nombre en m² ou null,
                    "nombre_pieces": nombre ou null
                },
                "locataire": {
                    "nom": "Prénom et Nom du locataire",
                    "email": "email@example.com ou null",
                    "telephone": "numéro ou null"
                },
                "bailleur": {
                    "nom": "Nom du bailleur/propriétaire/agence",
                    "adresse": "adresse ou null"
                },
                "pieces": [
                    {
                        "nom": "Nom exact de la pièce tel qu'écrit dans le document",
                        "observations": "Observations générales de la pièce ou null",
                        "elements": [
                            {
                                "nom": "Nom exact de l'élément",
                                "type": "sol|mur|plafond|menuiserie|electricite|plomberie|chauffage|equipement|mobilier|electromenager|autre",
                                "etat": "neuf|bon_etat|etat_moyen|mauvais_etat",
                                "observations": "TOUTES les observations, remarques, commentaires - COPIER MOT POUR MOT",
                                "photo_indices": [1, 2] (numéros des photos associées) ou []
                            }
                        ]
                    }
                ],
                "compteurs": {
                    "electricite": {"numero": "xxx", "index": "xxx"},
                    "gaz": {"numero": "xxx", "index": "xxx"},
                    "eau_froide": {"numero": "xxx", "index": "xxx"},
                    "eau_chaude": {"numero": "xxx", "index": "xxx"}
                },
                "cles": [
                    {"type": "Porte d'entrée", "nombre": 2},
                    {"type": "Boîte aux lettres", "nombre": 1}
                ],
                "observations_generales": "Toutes les observations générales du document"
            }

            EXEMPLES D'OBSERVATIONS À EXTRAIRE :
            - "Parquet vitrifié en excellent état" → observations: "Parquet vitrifié en excellent état"
            - "Légères traces d'usure" → observations: "Légères traces d'usure"
            - "RAS" → observations: "RAS"
            - "Fonctionne" → observations: "Fonctionne"
            - "À nettoyer" → observations: "À nettoyer"
            - "Tâche sur le mur côté fenêtre" → observations: "Tâche sur le mur côté fenêtre"
            - "(Photo 1)" → observations contient "(Photo 1)", photo_indices: [1]
            {$photoInstruction}

            IMPORTANT : Ne laisse AUCUNE observation vide si le document en contient une !
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

    /**
     * Sauvegarder une photo extraite et retourner le chemin
     */
    public function saveExtractedPhoto(string $tempPath): ?string
    {
        if (!file_exists($tempPath)) {
            return null;
        }

        $filename = 'photos/' . uniqid() . '_imported.png';
        $content = file_get_contents($tempPath);
        
        Storage::disk('public')->put($filename, $content);
        
        // Supprimer le fichier temporaire
        unlink($tempPath);

        return $filename;
    }

    /**
     * Nettoyer les photos temporaires
     */
    public function cleanupTempPhotos(array $photos): void
    {
        foreach ($photos as $photo) {
            if (isset($photo['path']) && file_exists($photo['path'])) {
                unlink($photo['path']);
            }
        }
        
        // Nettoyer les dossiers temp vides
        $tempDirs = glob(storage_path('app/temp/photos_*'), GLOB_ONLYDIR);
        foreach ($tempDirs as $dir) {
            if (is_dir($dir) && count(glob($dir . '/*')) === 0) {
                rmdir($dir);
            }
        }
    }
}