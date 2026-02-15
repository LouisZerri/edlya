#!/usr/bin/env php
<?php
/**
 * Test du mapping photo pour un PDF donné.
 * Usage: php tests/test_photo_mapping.php [chemin.pdf]
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ImportPdfService;
use App\Services\ImportValidationService;

$pdfPath = $argv[1] ?? __DIR__ . '/test.pdf';

if (!file_exists($pdfPath)) {
    echo "Fichier non trouvé: $pdfPath\n";
    exit(1);
}

echo "Analyse de: $pdfPath\n\n";

$service = new ImportPdfService();
$validator = new ImportValidationService();

$data = $service->analyserPdf($pdfPath);

$photos = $data['_extracted_photos'] ?? [];
unset($data['_extracted_photos']);

echo "=== PHOTOS GARDÉES PAR LE FILTRE: " . count($photos) . " ===\n";
foreach ($photos as $i => $p) {
    echo "  Index " . ($i + 1) . ": " . basename($p['path']) . " (" . $p['width'] . "x" . $p['height'] . ")\n";
}

echo "\n=== PHOTO_INDICES RETOURNÉS PAR L'IA ===\n";

// Compteurs
echo "\nCompteurs:\n";
foreach ($data['compteurs'] ?? [] as $type => $c) {
    $pi = $c['photo_indices'] ?? [];
    echo "  $type: photo_indices=" . json_encode($pi) . " | index=" . ($c['index'] ?? 'null') . "\n";
}

// Clés
echo "\nClés:\n";
foreach ($data['cles'] ?? [] as $cle) {
    $pi = $cle['photo_indices'] ?? [];
    echo "  " . ($cle['type'] ?? '?') . " (x" . ($cle['nombre'] ?? '?') . "): photo_indices=" . json_encode($pi) . "\n";
}

// Pièces et éléments
echo "\nPièces:\n";
foreach ($data['pieces'] ?? [] as $piece) {
    $pPhotos = $piece['photo_indices'] ?? [];
    echo "\n  " . ($piece['nom'] ?? '?') . " → piece_photos=" . json_encode($pPhotos) . "\n";
    foreach ($piece['elements'] ?? [] as $elem) {
        $ePhotos = $elem['photo_indices'] ?? [];
        if (!empty($ePhotos)) {
            echo "    " . ($elem['nom'] ?? '?') . " → " . json_encode($ePhotos) . "\n";
        }
    }
}

// Vérification : photos du PDF vs photos référencées
echo "\n\n=== VÉRIFICATION MAPPING ===\n";
$allReferenced = [];

foreach ($data['compteurs'] ?? [] as $c) {
    foreach ($c['photo_indices'] ?? [] as $pi) $allReferenced[$pi] = 'compteur';
}
foreach ($data['cles'] ?? [] as $cle) {
    foreach ($cle['photo_indices'] ?? [] as $pi) $allReferenced[$pi] = 'clé: ' . ($cle['type'] ?? '?');
}
foreach ($data['pieces'] ?? [] as $piece) {
    foreach ($piece['photo_indices'] ?? [] as $pi) $allReferenced[$pi] = 'pièce: ' . ($piece['nom'] ?? '?');
    foreach ($piece['elements'] ?? [] as $elem) {
        foreach ($elem['photo_indices'] ?? [] as $pi) $allReferenced[$pi] = ($piece['nom'] ?? '?') . ' > ' . ($elem['nom'] ?? '?');
    }
}

ksort($allReferenced);

echo "\nLe PDF contient les légendes Photo 1 à Photo 23 selon pdftotext.\n";
echo "Le filtre a gardé " . count($photos) . " photos.\n";
echo "L'IA référence " . count($allReferenced) . " photos distinctes.\n\n";

echo str_pad("Photo #", 10) . " | " . str_pad("Filtre", 8) . " | Assignée à\n";
echo str_repeat("─", 70) . "\n";

$maxIdx = max(23, count($photos), empty($allReferenced) ? 0 : max(array_keys($allReferenced)));
for ($i = 1; $i <= $maxIdx; $i++) {
    $inFilter = ($i <= count($photos)) ? '✅' : '❌';
    $assignedTo = $allReferenced[$i] ?? '-';
    echo str_pad("Photo $i", 10) . " | " . str_pad($inFilter, 8) . " | $assignedTo\n";
}

// Appliquer la validation
$data = $validator->validate($data, count($photos));
file_put_contents(__DIR__ . '/test_nockee_result.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Nettoyer
$service->cleanupTempPhotos($photos);

echo "\nJSON sauvegardé dans tests/test_nockee_result.json\n";
