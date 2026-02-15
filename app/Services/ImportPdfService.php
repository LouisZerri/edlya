<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportPdfService
{
    private ?string $apiKey = null;
    private string $model;
    private int $maxTokens;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('anthropic.api_key');
        $this->model = config('anthropic.model', 'claude-sonnet-4-20250514');
        $this->maxTokens = config('anthropic.max_tokens', 16000);
        $this->timeout = config('anthropic.timeout', 180);
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

        // Détecter le logiciel source
        $sourceFormat = $this->detectSourceFormat($pdfPath);
        if ($sourceFormat) {
            Log::info('Import PDF - Format source détecté', ['format' => $sourceFormat]);
        }

        // Construire les messages
        $systemMessage = $this->buildSystemMessage(count($extractedPhotos));
        $userContent = $this->buildUserContent($images, count($extractedPhotos), $sourceFormat);

        // Appel API avec retry
        $data = $this->callClaudeApiWithRetry($systemMessage, $userContent);

        // Nettoyer les images de pages temporaires
        foreach ($images as $imagePath) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Log spécifique pour les compteurs
        Log::info('Import PDF - Compteurs extraits par IA', [
            'compteurs' => $data['compteurs'] ?? 'NON TROUVÉ',
        ]);

        // Ajouter les photos extraites aux données
        $data['_extracted_photos'] = $extractedPhotos;

        return $data;
    }

    /**
     * Détecter le logiciel source du PDF via pdftotext
     */
    private function detectSourceFormat(string $pdfPath): ?string
    {
        $command = sprintf('pdftotext -l 2 %s - 2>/dev/null', escapeshellarg($pdfPath));
        $output = shell_exec($command);

        if (!$output) {
            return null;
        }

        $output = mb_strtolower($output);

        $formats = [
            'homepad' => 'Homepad',
            'immopad' => 'Immopad',
            'startloc' => 'Startloc',
            'edlsoft' => 'EDLSoft',
            'chapps' => 'Chapps',
            'clic & go' => 'Clic & Go',
            'onedl' => 'OneDL',
            'check & visit' => 'Check & Visit',
            'igloo' => 'Igloo',
        ];

        foreach ($formats as $keyword => $name) {
            if (str_contains($output, $keyword)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Appel API Claude avec retry (max 2 tentatives)
     */
    private function callClaudeApiWithRetry(string $systemMessage, array $userContent): array
    {
        $data = null;
        $lastError = null;

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $messages = [
                    ['role' => 'user', 'content' => $userContent],
                ];

                // En cas de retry, ajouter un message de relance
                if ($attempt > 1) {
                    Log::warning('Import PDF - Retry tentative ' . $attempt, ['error' => $lastError]);
                    $messages[] = [
                        'role' => 'assistant',
                        'content' => "Je vais réessayer l'extraction en produisant un JSON complet et valide.",
                    ];
                    $messages[] = [
                        'role' => 'user',
                        'content' => "La réponse précédente était invalide ({$lastError}). Produis un JSON complet et valide. Assure-toi que toutes les pièces et éléments sont inclus, et que le JSON se termine correctement avec toutes les accolades/crochets fermants.",
                    ];
                }

                $textContent = $this->callClaudeApi($systemMessage, $messages);
                $data = $this->parseJsonResponse($textContent);

                // Vérifier que le résultat est complet
                if (empty($data['pieces']) && empty($data['logement'])) {
                    throw new \Exception('JSON incomplet: pas de pièces ni de logement');
                }

                Log::info('Import PDF - Extraction réussie', ['attempt' => $attempt]);
                return $data;

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning('Import PDF - Tentative ' . $attempt . ' échouée', ['error' => $lastError]);

                if ($attempt >= 2) {
                    throw $e;
                }
            }
        }

        throw new \Exception('Échec de l\'extraction après 2 tentatives: ' . $lastError);
    }

    /**
     * Appel brut à l'API Claude
     */
    private function callClaudeApi(string $systemMessage, array $messages): string
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout($this->timeout)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'system' => $systemMessage,
            'messages' => $messages,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erreur API Anthropic: ' . $response->body());
        }

        $result = $response->json();
        $textContent = $result['content'][0]['text'] ?? '';

        Log::info('Import PDF - Réponse IA brute', [
            'response_length' => strlen($textContent),
            'stop_reason' => $result['stop_reason'] ?? 'unknown',
        ]);

        // Vérifier si la réponse a été tronquée
        if (($result['stop_reason'] ?? '') === 'max_tokens') {
            Log::warning('Import PDF - Réponse tronquée (max_tokens atteint)');
        }

        return $textContent;
    }

    /**
     * Construire le contenu du message user (images + instruction courte)
     */
    private function buildUserContent(array $images, int $photoCount, ?string $sourceFormat): array
    {
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

        $instruction = "Analyse ce PDF d'état des lieux et extrais toutes les données au format JSON selon le schéma défini.";

        if ($sourceFormat) {
            $instruction .= "\nCe document provient du logiciel {$sourceFormat}.";
        }

        if ($photoCount > 0) {
            $instruction .= "\n{$photoCount} photos ont été extraites du PDF (numérotées de 1 à {$photoCount}). Associe chaque photo au bon élément, compteur ou clé via photo_indices.";
        }

        $content[] = [
            'type' => 'text',
            'text' => $instruction,
        ];

        return $content;
    }

    /**
     * Construire le system message avec persona, schéma, règles et few-shot examples
     */
    private function buildSystemMessage(int $photoCount = 0): string
    {
        return <<<'SYSTEM'
            Tu es un expert en extraction de données depuis des PDF d'états des lieux immobiliers français.
            Tu dois analyser chaque page du document et produire un JSON structuré complet.

            ═══════════════════════════════════════════
            SCHÉMA JSON À PRODUIRE
            ═══════════════════════════════════════════

            Retourne UNIQUEMENT un objet JSON valide (sans ```json, sans commentaires) :

            {
                "type": "entree" ou "sortie",
                "date_realisation": "YYYY-MM-DD",
                "logement": {
                    "nom": "Type du bien (Appartement T3, Studio meublé, Maison, etc.)",
                    "adresse": "Numéro et rue UNIQUEMENT - SANS code postal ni ville",
                    "code_postal": "Code postal (ex: 33480)",
                    "ville": "Nom de la ville",
                    "type_bien": "appartement" ou "maison" ou "studio",
                    "surface": nombre en m² ou null,
                    "nombre_pieces": nombre ou null
                },
                "locataire": {
                    "nom": "Prénom et Nom du locataire",
                    "email": "email ou null",
                    "telephone": "numéro ou null"
                },
                "bailleur": {
                    "nom": "Nom du bailleur/propriétaire/agence",
                    "adresse": "adresse ou null"
                },
                "pieces": [
                    {
                        "nom": "Nom exact de la pièce",
                        "observations": "Observations générales ou null",
                        "photo_indices": [],
                        "elements": [
                            {
                                "nom": "Nom exact de l'élément",
                                "type": "sol|mur|plafond|menuiserie|electricite|plomberie|chauffage|equipement|mobilier|electromenager|autre",
                                "etat": "neuf|tres_bon|bon|usage|mauvais|hors_service",
                                "observations": "COPIER MOT POUR MOT",
                                "photo_indices": []
                            }
                        ]
                    }
                ],
                "compteurs": {
                    "electricite": {"numero": null, "index": null, "commentaire": null, "photo_indices": []},
                    "gaz": {"numero": null, "index": null, "commentaire": null, "photo_indices": []},
                    "eau_froide": {"numero": null, "index": null, "commentaire": null, "photo_indices": []},
                    "eau_chaude": {"numero": null, "index": null, "commentaire": null, "photo_indices": []}
                },
                "cles": [
                    {"type": "Porte d'entrée", "nombre": 2, "commentaire": null, "photo_indices": []}
                ],
                "observations_generales": "Observations générales ou null"
            }

            ═══════════════════════════════════════════
            ÉTATS : 6 NIVEAUX (très important)
            ═══════════════════════════════════════════

            Utilise EXACTEMENT ces 6 valeurs pour le champ "etat" :
            - "neuf" → Neuf, N, Excellent, Parfait état, état neuf
            - "tres_bon" → Très bon état, TB, TBE, Très bon, Bon état d'entretien
            - "bon" → Bon état, Bon, B, BE, Correct, Normal, RAS, Satisfaisant, État d'usage normal
            - "usage" → Usagé, U, Usure, Usage normal, Traces d'usure, État moyen, Passable, État d'usage
            - "mauvais" → Mauvais, M, ME, Mauvais état, Dégradé, Abîmé, Détérioré, Vétuste
            - "hors_service" → Hors service, HS, À remplacer, Hors d'usage, Non fonctionnel, Cassé

            Formats spéciaux courants dans les logiciels EDL :
            - Cases à cocher ☑/☐ avec colonnes N/B/U/M → N=neuf, B=bon, U=usage, M=mauvais
            - Échelle 1-4 : 1=neuf, 2=bon, 3=usage, 4=mauvais
            - Échelle 1-6 : 1=neuf, 2=tres_bon, 3=bon, 4=usage, 5=mauvais, 6=hors_service
            - Si aucun état n'est indiqué → "bon"

            ═══════════════════════════════════════════
            TYPES D'ÉLÉMENTS
            ═══════════════════════════════════════════

            - sol : parquet, carrelage, moquette, lino, vinyl, tomette, stratifié
            - mur : murs, peinture, papier peint, crépi, faïence murale, lambris
            - plafond : plafond, faux plafond, corniche
            - menuiserie : fenêtre, porte, volet, placard, porte-fenêtre, store, vitrage, serrure, poignée
            - electricite : prises, interrupteurs, luminaires, tableau électrique, spots, appliques, détecteur de fumée
            - plomberie : lavabo, douche, baignoire, WC, robinet, évier, siphon, tuyauterie, chasse d'eau, mitigeur
            - chauffage : radiateur, chaudière, convecteur, thermostat, climatisation, VMC, sèche-serviettes
            - equipement : plan de travail, hotte, plaques, four, crédence, étagères, miroir, barre de seuil
            - mobilier : lit, table, chaise, canapé, armoire, commode, bureau, bibliothèque
            - electromenager : réfrigérateur, lave-linge, lave-vaisselle, micro-ondes, sèche-linge, congélateur
            - autre : tout ce qui ne rentre pas dans les catégories ci-dessus

            ═══════════════════════════════════════════
            RÈGLES D'EXTRACTION
            ═══════════════════════════════════════════

            1. Lis CHAQUE PAGE du document attentivement
            2. Extrais TOUTES les observations mot pour mot - ne résume JAMAIS
            3. Si un élément a une observation ("RAS", "OK", "Bon état", etc.), elle DOIT apparaître
            4. Cherche les observations dans les colonnes : Observations, Remarques, Commentaires, Description, État
            5. ADRESSE : sépare toujours numéro+rue / code postal / ville
            6. COMPTEURS :
            - Numéro = N°, matricule, PDL, PCE, référence
            - Index = relevé, consommation. Si "non relevé" → null
            - "EAU" sans précision = eau_froide
            - Index composite → format texte : "HP : 7548 kWh, HC : 9808 kWh"
            7. CLÉS : section "REMISE/RESTITUTION DES CLÉS" → type + nombre + commentaire
            8. PHOTOS : associe les "Photo X" mentionnées dans le texte aux bons éléments via photo_indices
            9. Si un tableau est coupé entre 2 pages, FUSIONNER les données dans la même pièce
            10. DATE : toujours en format YYYY-MM-DD

            ═══════════════════════════════════════════
            FORMATS DE LOGICIELS CONNUS
            ═══════════════════════════════════════════

            Homepad/Immopad : Tableaux avec colonnes Désignation | Nature/Type | État | Observations. Photos légendées en bas de page.
            Startloc : Cases à cocher ☑ pour l'état (N/B/U/M). Observations dans colonne séparée.
            EDLSoft : Format texte structuré avec états entre parenthèses. Compteurs en fin de document.
            Chapps/OneDL : Format mixte tableau + texte libre.

            ═══════════════════════════════════════════
            EXEMPLES (few-shot)
            ═══════════════════════════════════════════

            --- Exemple 1 : Format tableau classique ---
            Entrée (extrait de tableau) :
            | Désignation | Nature | État | Observations |
            |-------------|--------|------|-------------|
            | Sol | Carrelage | Bon état | RAS |
            | Murs | Peinture blanche | Traces d'usure | Traces au-dessus radiateur |
            | Plafond | Peinture | Bon | - |
            | Porte | Bois | Bon état | Poignée légèrement rayée |

            Sortie attendue (extrait) :
            {"nom": "Sol", "type": "sol", "etat": "bon", "observations": "RAS", "photo_indices": []},
            {"nom": "Murs", "type": "mur", "etat": "usage", "observations": "Traces au-dessus radiateur", "photo_indices": []},
            {"nom": "Plafond", "type": "plafond", "etat": "bon", "observations": null, "photo_indices": []},
            {"nom": "Porte", "type": "menuiserie", "etat": "bon", "observations": "Poignée légèrement rayée", "photo_indices": []}

            --- Exemple 2 : Format cases à cocher ---
            Entrée (extrait) :
            Élément          | N | B | U | M | Observations
            Parquet          |   | ☑ |   |   | Quelques rayures superficielles
            Peinture murs    |   |   | ☑ |   | Traces de fixation, trous de chevilles
            Fenêtre PVC      | ☑ |   |   |   |
            Prises électriques|   | ☑ |   |   | 4 prises dont 1 sans cache

            Sortie attendue (extrait) :
            {"nom": "Parquet", "type": "sol", "etat": "bon", "observations": "Quelques rayures superficielles", "photo_indices": []},
            {"nom": "Peinture murs", "type": "mur", "etat": "usage", "observations": "Traces de fixation, trous de chevilles", "photo_indices": []},
            {"nom": "Fenêtre PVC", "type": "menuiserie", "etat": "neuf", "observations": null, "photo_indices": []},
            {"nom": "Prises électriques", "type": "electricite", "etat": "bon", "observations": "4 prises dont 1 sans cache", "photo_indices": []}

            --- Exemple 3 : Compteurs avec index composites ---
            Entrée (extrait) :
            COMPTEURS ET RELEVÉS
            Électricité - PDL : 16174095495231
            Index HP : 7 548 kWh / HC : 9 808 kWh
            Photos : Photo 1, Photo 2
            Eau froide - N° 532253812029817D
            Index : non relevé
            Observation : Compteur situé en sous-sol. Photo 3

            Sortie attendue (extrait) :
            "compteurs": {
                "electricite": {"numero": "16174095495231", "index": "HP : 7548 kWh, HC : 9808 kWh", "commentaire": null, "photo_indices": [1, 2]},
                "eau_froide": {"numero": "532253812029817D", "index": null, "commentaire": "Compteur situé en sous-sol.", "photo_indices": [3]},
                "gaz": {"numero": null, "index": null, "commentaire": null, "photo_indices": []},
                "eau_chaude": {"numero": null, "index": null, "commentaire": null, "photo_indices": []}
            }

            ═══════════════════════════════════════════

            IMPORTANT : Produis un JSON COMPLET et VALIDE. Ne tronque jamais la réponse. Assure-toi que toutes les accolades et crochets sont correctement fermés.
        SYSTEM;
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
            'pdftoppm -png -r 200 %s %s 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($outputPrefix)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $command = sprintf(
                'convert -density 200 %s %s/page-%%03d.png 2>&1',
                escapeshellarg($pdfPath),
                escapeshellarg($tempDir)
            );
            exec($command, $output, $returnCode);
        }

        $files = glob($tempDir . '/*.png');
        sort($files);

        return array_slice($files, 0, 20);
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
