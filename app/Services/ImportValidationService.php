<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ImportValidationService
{
    
    private const ETAT_MAPPING = [
        // Format BDD direct
        'neuf' => 'neuf',
        'tres_bon' => 'tres_bon',
        'bon' => 'bon',
        'usage' => 'usage',
        'mauvais' => 'mauvais',
        'hors_service' => 'hors_service',
        // Legacy 4-tier format
        'bon_etat' => 'bon',
        'etat_moyen' => 'usage',
        'mauvais_etat' => 'mauvais',
        // Variantes textuelles
        'très bon' => 'tres_bon',
        'très bon état' => 'tres_bon',
        'tres bon' => 'tres_bon',
        'tres bon etat' => 'tres_bon',
        'bon état' => 'bon',
        'bon etat' => 'bon',
        'état moyen' => 'usage',
        'etat moyen' => 'usage',
        'usagé' => 'usage',
        'usé' => 'usage',
        'use' => 'usage',
        'mauvais état' => 'mauvais',
        'mauvais etat' => 'mauvais',
        'dégradé' => 'mauvais',
        'degrade' => 'mauvais',
        'hors service' => 'hors_service',
        'hs' => 'hors_service',
        'à remplacer' => 'hors_service',
        'a remplacer' => 'hors_service',
        // Abbréviations logiciels (Homepad, Immopad, etc.)
        'n' => 'neuf',
        'tb' => 'tres_bon',
        'tbe' => 'tres_bon',
        'b' => 'bon',
        'be' => 'bon',
        'u' => 'usage',
        'm' => 'mauvais',
        'me' => 'mauvais',
        // Formats numérotés
        '1' => 'neuf',
        '2' => 'bon',
        '3' => 'usage',
        '4' => 'mauvais',
    ];

    private const VALID_ELEMENT_TYPES = [
        'sol', 'mur', 'plafond', 'menuiserie', 'electricite',
        'plomberie', 'chauffage', 'equipement', 'mobilier',
        'electromenager', 'autre',
    ];

    private const COMPTEUR_TYPE_MAPPING = [
        'electricite' => 'electricite',
        'électricité' => 'electricite',
        'electrique' => 'electricite',
        'eau_froide' => 'eau_froide',
        'eau_chaude' => 'eau_chaude',
        'eau' => 'eau_froide',
        'gaz' => 'gaz',
    ];

    /**
     * Valider et auto-corriger les données extraites par l'IA
     */
    public function validate(array $data, int $photoCount = 0): array
    {
        $corrections = [];

        $data = $this->normalizeDate($data, $corrections);
        $data = $this->normalizeAddress($data, $corrections);
        $data = $this->normalizeCompteurs($data, $corrections);
        $data = $this->normalizePieces($data, $photoCount, $corrections);

        if (!empty($corrections)) {
            Log::info('Import PDF - Auto-corrections appliquées', ['corrections' => $corrections]);
        }

        return $data;
    }

    private function normalizeDate(array $data, array &$corrections): array
    {
        if (!empty($data['date_realisation'])) {
            $date = $data['date_realisation'];

            // dd/mm/yyyy → yyyy-mm-dd
            if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $m)) {
                $data['date_realisation'] = "{$m[3]}-{$m[2]}-{$m[1]}";
                $corrections[] = "Date '{$date}' → '{$data['date_realisation']}'";
            }
            // dd-mm-yyyy → yyyy-mm-dd
            elseif (preg_match('#^(\d{2})-(\d{2})-(\d{4})$#', $date, $m)) {
                $data['date_realisation'] = "{$m[3]}-{$m[2]}-{$m[1]}";
                $corrections[] = "Date '{$date}' → '{$data['date_realisation']}'";
            }
        }

        return $data;
    }

    private function normalizeAddress(array $data, array &$corrections): array
    {
        if (empty($data['logement'])) {
            return $data;
        }

        $logement = &$data['logement'];

        // Extraire code postal de l'adresse s'il y est resté
        if (!empty($logement['adresse']) && empty($logement['code_postal'])) {
            if (preg_match('/(\d{5})\s+(.+)$/i', $logement['adresse'], $matches)) {
                $logement['code_postal'] = $matches[1];
                $logement['ville'] = $logement['ville'] ?? trim($matches[2]);
                $logement['adresse'] = trim(str_replace($matches[0], '', $logement['adresse']));
                $corrections[] = "Code postal '{$matches[1]}' extrait de l'adresse";
            }
        }

        // Nettoyer code postal et ville de l'adresse même si déjà renseignés
        if (!empty($logement['adresse']) && !empty($logement['code_postal'])) {
            $cp = preg_quote($logement['code_postal'], '/');
            $cleaned = preg_replace("/,?\s*{$cp}\s*.*/i", '', $logement['adresse']);
            if ($cleaned !== $logement['adresse']) {
                $logement['adresse'] = trim($cleaned, ', ');
                $corrections[] = "Adresse nettoyée (code postal/ville retirés)";
            }
        }

        return $data;
    }

    private function normalizeCompteurs(array $data, array &$corrections): array
    {
        if (empty($data['compteurs'])) {
            return $data;
        }

        $normalized = [];

        foreach ($data['compteurs'] as $type => $compteur) {
            $normalizedType = self::COMPTEUR_TYPE_MAPPING[strtolower($type)] ?? null;

            if (!$normalizedType) {
                $corrections[] = "Compteur type inconnu '{$type}' ignoré";
                continue;
            }

            if ($normalizedType !== $type) {
                $corrections[] = "Compteur type '{$type}' → '{$normalizedType}'";
            }

            $normalized[$normalizedType] = $compteur;
        }

        $data['compteurs'] = $normalized;

        return $data;
    }

    private function normalizePieces(array $data, int $photoCount, array &$corrections): array
    {
        if (empty($data['pieces'])) {
            return $data;
        }

        $pieces = [];
        $seenNames = [];

        foreach ($data['pieces'] as $piece) {
            // Supprimer les pièces sans nom
            if (empty($piece['nom'])) {
                $corrections[] = "Pièce sans nom supprimée";
                continue;
            }

            // Valider photo_indices de la pièce
            if (!empty($piece['photo_indices'])) {
                $piece['photo_indices'] = $this->filterPhotoIndices($piece['photo_indices'], $photoCount);
            }

            // Normaliser les éléments
            if (!empty($piece['elements'])) {
                $piece['elements'] = $this->normalizeElements($piece['elements'], $photoCount, $corrections);
            }

            // Fusion de pièces dupliquées (coupure de tableau entre 2 pages)
            $nameKey = mb_strtolower(trim($piece['nom']));
            if (isset($seenNames[$nameKey])) {
                $existingIdx = $seenNames[$nameKey];
                $pieces[$existingIdx] = $this->mergePieces($pieces[$existingIdx], $piece);
                $corrections[] = "Pièce dupliquée '{$piece['nom']}' fusionnée";
                continue;
            }

            $seenNames[$nameKey] = count($pieces);
            $pieces[] = $piece;
        }

        $data['pieces'] = $pieces;

        return $data;
    }

    private function normalizeElements(array $elements, int $photoCount, array &$corrections): array
    {
        $normalized = [];

        foreach ($elements as $element) {
            // Supprimer les éléments sans nom
            if (empty($element['nom'])) {
                $corrections[] = "Élément sans nom supprimé";
                continue;
            }

            // Normaliser l'état
            if (!empty($element['etat'])) {
                $normalizedEtat = $this->normalizeEtat($element['etat']);
                if ($normalizedEtat !== $element['etat']) {
                    $corrections[] = "État '{$element['etat']}' → '{$normalizedEtat}'";
                }
                $element['etat'] = $normalizedEtat;
            } else {
                $element['etat'] = 'bon';
            }

            // Normaliser le type
            if (empty($element['type']) || !in_array($element['type'], self::VALID_ELEMENT_TYPES)) {
                $oldType = $element['type'] ?? 'null';
                $element['type'] = 'autre';
                if ($oldType !== 'autre') {
                    $corrections[] = "Type élément '{$oldType}' → 'autre'";
                }
            }

            // Valider photo_indices
            if (!empty($element['photo_indices'])) {
                $element['photo_indices'] = $this->filterPhotoIndices($element['photo_indices'], $photoCount);
            }

            $normalized[] = $element;
        }

        return $normalized;
    }

    private function normalizeEtat(string $etat): string
    {
        $key = mb_strtolower(trim($etat));

        return self::ETAT_MAPPING[$key] ?? 'bon';
    }

    private function filterPhotoIndices(array $indices, int $photoCount): array
    {
        if ($photoCount === 0) {
            return $indices;
        }

        return array_values(array_filter($indices, fn($i) => is_int($i) && $i >= 1 && $i <= $photoCount));
    }

    private function mergePieces(array $existing, array $duplicate): array
    {
        // Fusionner les éléments
        if (!empty($duplicate['elements'])) {
            $existing['elements'] = array_merge($existing['elements'] ?? [], $duplicate['elements']);
        }

        // Fusionner les photo_indices
        if (!empty($duplicate['photo_indices'])) {
            $existing['photo_indices'] = array_merge($existing['photo_indices'] ?? [], $duplicate['photo_indices']);
        }

        // Concaténer les observations
        if (!empty($duplicate['observations'])) {
            $existing['observations'] = trim(($existing['observations'] ?? '') . ' ' . $duplicate['observations']);
        }

        return $existing;
    }
}
