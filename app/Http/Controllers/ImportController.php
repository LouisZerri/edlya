<?php

namespace App\Http\Controllers;

use App\Models\Cle;
use App\Models\EtatDesLieux;
use App\Models\Logement;
use App\Models\Piece;
use App\Models\Element;
use App\Models\Photo;
use App\Models\Compteur;
use App\Services\ImportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function create()
    {
        return view('etats-des-lieux.import');
    }

    public function analyze(Request $request, ImportPdfService $importService)
    {
        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        try {
            $file = $request->file('pdf');
            $fullPath = $file->getRealPath();

            $data = $importService->analyserPdf($fullPath);

            // Stocker les photos temporaires en session
            $extractedPhotos = $data['_extracted_photos'] ?? [];
            unset($data['_extracted_photos']);

            session(['imported_photos' => $extractedPhotos]);

            // Vérifier si le logement existe déjà
            $logementExistant = null;
            if (!empty($data['logement']['adresse'])) {
                $logementExistant = Logement::where('user_id', Auth::id())
                    ->where('adresse', 'LIKE', '%' . $data['logement']['adresse'] . '%')
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'photos_count' => count($extractedPhotos),
                'logement_existant' => $logementExistant ? [
                    'id' => $logementExistant->id,
                    'nom' => $logementExistant->nom,
                    'adresse' => $logementExistant->adresse,
                ] : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function store(Request $request, ImportPdfService $importService)
    {
        $request->validate([
            'data' => ['required', 'array'],
            'logement_id' => ['nullable', 'exists:logements,id'],
        ]);

        $data = $request->input('data');
        $extractedPhotos = session('imported_photos', []);

        try {
            DB::beginTransaction();

            // Créer ou récupérer le logement
            if ($request->filled('logement_id')) {
                $logement = Logement::where('user_id', Auth::id())
                    ->findOrFail($request->input('logement_id'));
            } else {
                $adresse = $data['logement']['adresse'] ?? '';
                $codePostal = '00000';
                $ville = 'Non renseignée';

                if (preg_match('/(\d{5})\s+(.+)$/i', $adresse, $matches)) {
                    $codePostal = $matches[1];
                    $ville = trim($matches[2]);
                }

                $nom = $adresse;
                if (preg_match('/^(.+?),?\s*\d{5}/i', $adresse, $matchesNom)) {
                    $nom = trim($matchesNom[1], ', ');
                }

                $logement = Logement::create([
                    'user_id' => Auth::id(),
                    'nom' => $data['logement']['nom'] ?? $nom ?: 'Logement importé',
                    'adresse' => $adresse,
                    'code_postal' => $codePostal,
                    'ville' => $ville,
                    'type' => $data['logement']['type_bien'] ?? 'appartement',
                    'surface' => $data['logement']['surface'] ?? null,
                    'nb_pieces' => $data['logement']['nombre_pieces'] ?? null,
                ]);
            }

            // Créer l'état des lieux
            $etatDesLieux = EtatDesLieux::create([
                'user_id' => Auth::id(),
                'logement_id' => $logement->id,
                'type' => $data['type'] ?? 'entree',
                'date_realisation' => $data['date_realisation'] ?? now()->format('Y-m-d'),
                'locataire_nom' => $data['locataire']['nom'] ?? 'Non renseigné',
                'locataire_email' => $data['locataire']['email'] ?? null,
                'locataire_telephone' => $data['locataire']['telephone'] ?? null,
                'observations_generales' => $data['observations_generales'] ?? null,
                'statut' => 'brouillon',
            ]);

            // Créer les compteurs
            if (!empty($data['compteurs'])) {
                $typesCompteurs = ['electricite', 'eau_froide', 'eau_chaude', 'gaz'];

                foreach ($typesCompteurs as $type) {
                    if (!empty($data['compteurs'][$type])) {
                        $compteurData = $data['compteurs'][$type];

                        $numero = $compteurData['numero'] ?? null;
                        $index = $compteurData['index'] ?? null;

                        $photoIndices = $compteurData['photo_indices'] ?? [];

                        if ($numero || $index || !empty($photoIndices)) {
                            $photoPath = null;

                            if (!empty($photoIndices)) {
                                $photoIndex = $photoIndices[0];
                                $arrayIndex = $photoIndex - 1;

                                if (isset($extractedPhotos[$arrayIndex]) && empty($extractedPhotos[$arrayIndex]['used'])) {
                                    $tempPhotoPath = $extractedPhotos[$arrayIndex]['path'] ?? null;

                                    if ($tempPhotoPath && file_exists($tempPhotoPath)) {
                                        $filename = 'compteurs/' . uniqid() . '_imported.png';
                                        $content = file_get_contents($tempPhotoPath);
                                        Storage::disk('public')->put($filename, $content);

                                        $photoPath = $filename;
                                        $extractedPhotos[$arrayIndex]['used'] = true;
                                        unlink($tempPhotoPath);
                                    }
                                }
                            }

                            Compteur::create([
                                'etat_des_lieux_id' => $etatDesLieux->id,
                                'type' => $type,
                                'numero' => $numero,
                                'index' => $index,
                                'commentaire' => $compteurData['commentaire'] ?? null,
                                'photo' => $photoPath,
                            ]);
                        }
                    }
                }
            }

            // Créer les clés
            if (!empty($data['cles'])) {
                foreach ($data['cles'] as $cleData) {
                    if (!empty($cleData['type'])) {
                        $photoPath = null;

                        // Gérer la photo de la clé
                        $photoIndices = $cleData['photo_indices'] ?? [];
                        if (!empty($photoIndices)) {
                            $photoIndex = $photoIndices[0];
                            $arrayIndex = $photoIndex - 1;

                            if (isset($extractedPhotos[$arrayIndex]) && empty($extractedPhotos[$arrayIndex]['used'])) {
                                $tempPhotoPath = $extractedPhotos[$arrayIndex]['path'] ?? null;

                                if ($tempPhotoPath && file_exists($tempPhotoPath)) {
                                    $filename = 'cles/' . uniqid() . '_imported.png';
                                    $content = file_get_contents($tempPhotoPath);
                                    Storage::disk('public')->put($filename, $content);

                                    $photoPath = $filename;
                                    $extractedPhotos[$arrayIndex]['used'] = true;
                                    unlink($tempPhotoPath);
                                }
                            }
                        }

                        Cle::create([
                            'etat_des_lieux_id' => $etatDesLieux->id,
                            'type' => $cleData['type'],
                            'nombre' => $cleData['nombre'] ?? 1,
                            'commentaire' => $cleData['commentaire'] ?? null,
                            'photo' => $photoPath,
                        ]);
                    }
                }
            }

            // Créer les pièces et éléments avec photos
            if (!empty($data['pieces'])) {
                foreach ($data['pieces'] as $ordre => $pieceData) {
                    $piece = Piece::create([
                        'etat_des_lieux_id' => $etatDesLieux->id,
                        'nom' => $pieceData['nom'],
                        'ordre' => $ordre + 1,
                    ]);

                    if (!empty($pieceData['elements'])) {
                        foreach ($pieceData['elements'] as $elementOrdre => $elementData) {
                            $etat = $this->convertEtat($elementData['etat'] ?? 'bon_etat');

                            $element = Element::create([
                                'piece_id' => $piece->id,
                                'nom' => $elementData['nom'],
                                'type' => $elementData['type'] ?? 'autre',
                                'etat' => $etat,
                                'observations' => $elementData['observations'] ?? null,
                            ]);

                            $photoIndices = $elementData['photo_indices'] ?? [];
                            foreach ($photoIndices as $photoIndex) {
                                $arrayIndex = $photoIndex - 1;

                                if (isset($extractedPhotos[$arrayIndex]) && empty($extractedPhotos[$arrayIndex]['used'])) {
                                    $photoPath = $extractedPhotos[$arrayIndex]['path'] ?? null;

                                    if ($photoPath) {
                                        $savedPath = $importService->saveExtractedPhoto($photoPath);

                                        if ($savedPath) {
                                            Photo::create([
                                                'element_id' => $element->id,
                                                'chemin' => $savedPath,
                                            ]);

                                            $extractedPhotos[$arrayIndex]['used'] = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Nettoyer les photos non utilisées
            $importService->cleanupTempPhotos($extractedPhotos);
            session()->forget('imported_photos');

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('etats-des-lieux.edit', $etatDesLieux),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $importService->cleanupTempPhotos($extractedPhotos);
            session()->forget('imported_photos');

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Convertir l'état du format IA vers le format BDD
     */
    private function convertEtat(string $etat): string
    {
        $mapping = [
            'neuf' => 'neuf',
            'bon_etat' => 'tres_bon',
            'tres_bon' => 'tres_bon',
            'bon' => 'bon',
            'etat_moyen' => 'usage',
            'usage' => 'usage',
            'mauvais_etat' => 'mauvais',
            'mauvais' => 'mauvais',
            'hors_service' => 'hors_service',
        ];

        return $mapping[$etat] ?? 'bon';
    }
}