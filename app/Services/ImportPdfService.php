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

        Log::info('Import PDF - Pages converties', ['count' => count($images)]);

        // Extraire les photos du PDF
        $extractedPhotos = $this->extractPhotosFromPdf($pdfPath);

        Log::info('Import PDF - Photos extraites après filtrage', [
            'count' => count($extractedPhotos),
            'photos' => array_map(fn($p) => [
                'path' => basename($p['path']),
                'width' => $p['width'],
                'height' => $p['height'],
                'size_kb' => round(filesize($p['path']) / 1024, 2),
            ], $extractedPhotos),
        ]);

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

        Log::info('Import PDF - Réponse IA brute', ['response' => $textContent]);

        $data = $this->parseJsonResponse($textContent);

        // Log spécifique pour les compteurs
        Log::info('Import PDF - Compteurs extraits par IA', [
            'compteurs' => $data['compteurs'] ?? 'NON TROUVÉ',
        ]);

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

        $command = sprintf(
            'pdfimages -png %s %s/photo 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($tempDir)
        );

        exec($command, $output, $returnCode);

        $photos = [];
        $allImages = [];

        if ($returnCode === 0) {
            $files = glob($tempDir . '/*.png');
            sort($files);

            foreach ($files as $index => $file) {
                $imageInfo = getimagesize($file);
                $fileSize = filesize($file);

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    $pixels = $width * $height;
                    $ratio = $height > 0 ? $width / $height : 0;
                    $bytesPerPixel = $pixels > 0 ? $fileSize / $pixels : 0;

                    $imageData = [
                        'index' => $index,
                        'file' => basename($file),
                        'width' => $width,
                        'height' => $height,
                        'ratio' => round($ratio, 2),
                        'size_kb' => round($fileSize / 1024, 2),
                        'bytes_per_pixel' => round($bytesPerPixel, 3),
                        'status' => 'pending',
                    ];

                    // Critères de filtrage
                    $isValidSize = $width >= 200 && $height >= 200;
                    $isNotTooLarge = $width < 3000 && $height < 3000;
                    $isValidRatio = $ratio >= 0.5 && $ratio <= 2;
                    $isNotTooSmallFile = $fileSize > 10000;
                    $isRealPhoto = $bytesPerPixel > 0.15;

                    // Exclure les images parfaitement carrées (logos, avatars)
                    $isNotSquare = abs($ratio - 1.0) > 0.05;

                    if ($isValidSize && $isNotTooLarge && $isValidRatio && $isNotTooSmallFile && $isRealPhoto && $isNotSquare) {
                        $photos[] = [
                            'path' => $file,
                            'width' => $width,
                            'height' => $height,
                        ];
                        $imageData['status'] = 'KEPT';
                    } else {
                        $imageData['status'] = 'REJECTED';
                        $imageData['reasons'] = [];
                        if (!$isValidSize) $imageData['reasons'][] = 'trop_petit';
                        if (!$isNotTooLarge) $imageData['reasons'][] = 'trop_grand';
                        if (!$isValidRatio) $imageData['reasons'][] = 'ratio_invalide';
                        if (!$isNotTooSmallFile) $imageData['reasons'][] = 'fichier_trop_leger';
                        if (!$isRealPhoto) $imageData['reasons'][] = 'logo_detecte_bytes_per_pixel';
                        if (!$isNotSquare) $imageData['reasons'][] = 'image_carree_suspecte';
                        unlink($file);
                    }

                    $allImages[] = $imageData;
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
                PHOTOS EXTRAITES : {$photoCount} photos ont été extraites du PDF.
                - Les photos sont numérotées de 1 à {$photoCount} dans l'ordre où elles apparaissent dans le document.
                - Cherche les légendes des photos : 'Photo 1 - Eau', 'Photo 2 - Cuisine', 'Compteur électrique', etc.
                - Associe chaque photo au bon élément, compteur ou clé via photo_indices.";
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
                    "adresse": "Numéro et rue UNIQUEMENT (ex: 11 rue du Docteur Roux, porte 4) - SANS code postal ni ville",
                    "code_postal": "Code postal (ex: 33480)",
                    "ville": "Nom de la ville (ex: Castelnau de Médoc)",
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
                        "photo_indices": [6, 7, 8, 9, 10],
                        "elements": [
                            {
                                "nom": "Nom exact de l'élément",
                                "type": "sol|mur|plafond|menuiserie|electricite|plomberie|chauffage|equipement|mobilier|electromenager|autre",
                                "etat": "neuf|bon_etat|etat_moyen|mauvais_etat",
                                "observations": "TOUTES les observations - COPIER MOT POUR MOT",
                                "photo_indices": [1, 2] ou []
                            }
                        ]
                    }
                ],
                "compteurs": {
                    "electricite": {"numero": "numéro ou null", "index": "relevé ou null", "commentaire": "observations complètes ou null", "photo_indices": []},
                    "gaz": {"numero": "numéro ou null", "index": "relevé ou null", "commentaire": "observations complètes ou null", "photo_indices": []},
                    "eau_froide": {"numero": "numéro ou null", "index": "relevé ou null", "commentaire": "observations complètes ou null", "photo_indices": []},
                    "eau_chaude": {"numero": "numéro ou null", "index": "relevé ou null", "commentaire": "observations complètes ou null", "photo_indices": []}
                },
                "cles": [
                    {"type": "Porte d'entrée", "nombre": 2, "commentaire": "observations ou null", "photo_indices": [21]},
                    {"type": "Boîte aux lettres", "nombre": 1, "commentaire": null, "photo_indices": [23]}
                ],
                "observations_generales": "Toutes les observations générales du document"
            }

            ADRESSE - TRÈS IMPORTANT :
            - "adresse" = SEULEMENT le numéro et la rue (ex: "11 rue du Docteur Roux, porte 4")
            - "code_postal" = SEULEMENT le code postal (ex: "33480")
            - "ville" = SEULEMENT le nom de la ville (ex: "Castelnau de Médoc")
            - NE PAS inclure le code postal ou la ville dans le champ "adresse"

            COMPTEURS - TRÈS IMPORTANT :
            - Extraire le numéro du compteur (N°, numéro, matricule, PDL, PCE, etc.)
            - Extraire l'index/relevé. Si "non relevé" ou vide → mettre null pour index
            - Extraire TOUTES les observations mot pour mot dans "commentaire"
            - Le compteur "EAU" sans précision = eau_froide

            PHOTOS DE COMPTEURS - CRITIQUE :
            - Cherche les mentions "Photo X" dans les observations des compteurs
            - Cherche les photos légendées dans le document : "Photo 1 - Eau", "Photo 2 - Electricité", "Compteur électrique", etc.
            - RÈGLE : Si l'observation d'un compteur contient "Photo 1" ET qu'une photo est légendée "Photo 1 - Eau" ou similaire :
            → Ajouter 1 dans photo_indices du compteur correspondant
            - MÊME si le relevé est "non relevé", s'il y a une référence photo, TOUJOURS remplir photo_indices
            - Un compteur peut avoir PLUSIEURS photos (ex: photo_indices: [1, 2, 3, 4])

            PHOTOS GÉNÉRALES DE PIÈCES - IMPORTANT :
            - Cherche la mention "Ont été prises les photos suivantes concernant la pièce en général : Photo X Photo Y..."
            - Ces photos générales doivent aller dans photo_indices au niveau de la PIÈCE (pas des éléments)
            - Exemple : SALON avec "Photo 6 Photo 7 Photo 8 Photo 9 Photo 10" → piece.photo_indices = [6, 7, 8, 9, 10]
            - Les photos d'éléments spécifiques (Four, Mur, etc.) vont dans les photo_indices de l'élément concerné

            CLÉS - TRÈS IMPORTANT :
            - Cherche la section "REMISE DES CLÉS" ou "RESTITUTION DES CLÉS" dans le document
            - Extrait chaque type de clé avec son nombre
            - Cherche les mentions "Photo X" dans la colonne observations/commentaires des clés
            - Si une clé a une photo associée (ex: "Photo 21 - Porte principale"), ajoute l'indice dans photo_indices
            - Extrait aussi tout commentaire/observation associé à chaque clé

            EXEMPLE COMPTEUR :
            - Compteur EAU avec numéro "532253812029817D", relevé "non relevé", observation "Compteur N° 4 relevés sur photos Photo 1"
            - Photo légendée "Photo 1 - Eau" visible dans le document
            → eau_froide = {
                "numero": "532253812029817D", 
                "index": null, 
                "commentaire": "Compteur N° 4 relevés sur photos Photo 1", 
                "photo_indices": [1]
            }

            EXEMPLE COMPTEUR AVEC PLUSIEURS PHOTOS :
            - Compteur ÉLECTRICITÉ avec photos "Photo 1 Photo 2 Photo 3 Photo 4" dans les observations
            → electricite = {
                "numero": "16174095495231",
                "index": "HP : 7548 kWh, HC : 9808 kWh",
                "commentaire": null,
                "photo_indices": [1, 2, 3, 4]
            }

            EXEMPLE PIÈCE AVEC PHOTOS GÉNÉRALES :
            - SALON avec "Ont été prises les photos suivantes concernant la pièce en général : Photo 6 Photo 7 Photo 8 Photo 9 Photo 10"
            → piece = {
                "nom": "SALON",
                "observations": null,
                "photo_indices": [6, 7, 8, 9, 10],
                "elements": [...]
            }

            EXEMPLE CLÉS :
            - "Porte principale" - 2 clés - Photo 40
            - "Parties communes" - 1 clé - Photo 41
            → cles = [
                {"type": "Porte principale", "nombre": 2, "commentaire": null, "photo_indices": [40]},
                {"type": "Parties communes", "nombre": 1, "commentaire": null, "photo_indices": [41]}
            ]
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
