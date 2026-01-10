<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Models\Logement;
use App\Models\Piece;
use App\Models\Element;
use App\Services\ImportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            // Utiliser directement le fichier temporaire uploadé
            $fullPath = $file->getRealPath();

            $data = $importService->analyserPdf($fullPath);

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

    public function store(Request $request)
    {
        $request->validate([
            'data' => ['required', 'array'],
            'logement_id' => ['nullable', 'exists:logements,id'],
        ]);

        $data = $request->input('data');

        try {
            DB::beginTransaction();

            // Créer ou récupérer le logement
            if ($request->filled('logement_id')) {
                $logement = Logement::where('user_id', Auth::id())
                    ->findOrFail($request->input('logement_id'));
            } else {
                // Extraire ville et code postal de l'adresse
                $adresse = $data['logement']['adresse'] ?? '';
                $codePostal = '00000';
                $ville = 'Non renseignée';

                // Chercher pattern: 33480 Castelnau de Médoc
                if (preg_match('/(\d{5})\s+(.+)$/i', $adresse, $matches)) {
                    $codePostal = $matches[1];
                    $ville = trim($matches[2]);
                }

                // Extraire le nom (partie avant le code postal)
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

            // Créer les pièces et éléments
            if (!empty($data['pieces'])) {
                foreach ($data['pieces'] as $ordre => $pieceData) {
                    $piece = Piece::create([
                        'etat_des_lieux_id' => $etatDesLieux->id,
                        'nom' => $pieceData['nom'],
                        'ordre' => $ordre + 1,
                    ]);

                    if (!empty($pieceData['elements'])) {
                        foreach ($pieceData['elements'] as $elementOrdre => $elementData) {
                            Element::create([
                                'piece_id' => $piece->id,
                                'nom' => $elementData['nom'],
                                'type' => $elementData['type'] ?? 'autre',
                                'quantite' => $elementData['quantite'] ?? 1,
                                'etat_entree' => $data['type'] === 'entree' ? ($elementData['etat'] ?? 'bon_etat') : null,
                                'etat_sortie' => $data['type'] === 'sortie' ? ($elementData['etat'] ?? 'bon_etat') : null,
                                'observations_entree' => $data['type'] === 'entree' ? ($elementData['observations'] ?? null) : null,
                                'observations_sortie' => $data['type'] === 'sortie' ? ($elementData['observations'] ?? null) : null,
                                'ordre' => $elementOrdre + 1,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('etats-des-lieux.edit', $etatDesLieux),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
