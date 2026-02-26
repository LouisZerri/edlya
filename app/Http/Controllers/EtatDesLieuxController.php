<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DeletesEtatDesLieux;
use App\Http\Requests\GenererPiecesRequest;
use App\Models\EtatDesLieux;
use App\Models\Logement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatDesLieuxController extends Controller
{
    use DeletesEtatDesLieux;
    /**
     * Liste des états des lieux
     */
    public function index()
    {
        $etatsDesLieux = EtatDesLieux::with('logement')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('etats-des-lieux.index', compact('etatsDesLieux'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $logements = Logement::where('user_id', Auth::id())->get();

        return view('etats-des-lieux.create', [
            'logements' => $logements,
            'logementSelectionne' => request('logement_id'),
        ]);
    }

    /**
     * Enregistrement d’un nouvel état des lieux
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logement_id' => ['required', 'exists:logement,id'],
            'type' => ['required', 'in:entree,sortie'],
            'date_realisation' => ['required', 'date'],
            'locataire_nom' => ['required', 'string', 'max:255'],
            'locataire_email' => ['nullable', 'email', 'max:255'],
            'locataire_telephone' => ['nullable', 'string', 'max:20'],
            'observations_generales' => ['nullable', 'string'],
            'autres_locataires' => ['nullable', 'array'],
            'autres_locataires.*' => ['string', 'max:255'],
            'typologie' => ['nullable', 'string'],
        ]);

        // Vérifier que le logement appartient à l'utilisateur
        $logement = Logement::where('user_id', Auth::id())
            ->findOrFail($validated['logement_id']);

        // Créer l'état des lieux
        $etatDesLieux = EtatDesLieux::create([
            'user_id' => Auth::id(),
            'logement_id' => $validated['logement_id'],
            'type' => $validated['type'],
            'date_realisation' => $validated['date_realisation'],
            'locataire_nom' => $validated['locataire_nom'],
            'locataire_email' => $validated['locataire_email'] ?? null,
            'locataire_telephone' => $validated['locataire_telephone'] ?? null,
            'observations_generales' => $validated['observations_generales'] ?? null,
            'autres_locataires' => !empty($validated['autres_locataires']) ? array_values($validated['autres_locataires']) : null,
            'statut' => 'brouillon',
        ]);

        // Générer les pièces selon la typologie
        if (!empty($validated['typologie'])) {
            $typologies = config('typologies');

            if (isset($typologies[$validated['typologie']])) {
                $pieces = $typologies[$validated['typologie']]['pieces'];

                foreach ($pieces as $ordre => $nomPiece) {
                    $etatDesLieux->pieces()->create([
                        'nom' => $nomPiece,
                        'ordre' => $ordre + 1,
                    ]);
                }
            }
        }

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->with('success', 'État des lieux créé avec succès.');
    }


    /**
     * Affichage d'un état des lieux
     */
    public function show(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos', 'compteurs', 'cles']);

        return view('etats-des-lieux.show', compact('etatDesLieux'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('update', $etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos', 'compteurs', 'cles']);
        $logements = Logement::where('user_id', Auth::id())->get();

        return view('etats-des-lieux.edit', compact('etatDesLieux', 'logements'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, EtatDesLieux $etatDesLieux)
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'logement_id' => ['required', 'exists:logement,id'],
            'type' => ['required', 'in:entree,sortie'],
            'date_realisation' => ['required', 'date'],
            'locataire_nom' => ['required', 'string', 'max:255'],
            'locataire_email' => ['nullable', 'email', 'max:255'],
            'locataire_telephone' => ['nullable', 'string', 'max:20'],
            'observations_generales' => ['nullable', 'string'],
            'autres_locataires' => ['nullable', 'array'],
            'autres_locataires.*' => ['string', 'max:255'],
            'statut' => ['nullable', 'in:brouillon,en_cours,termine'],
        ]);

        $validated['autres_locataires'] = !empty($validated['autres_locataires']) ? array_values($validated['autres_locataires']) : null;

        $etatDesLieux->update($validated);

        return redirect()
            ->route('etats-des-lieux.show', $etatDesLieux)
            ->with('success', 'État des lieux mis à jour.');
    }

    /**
     * Suppression
     */
    public function destroy(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('delete', $etatDesLieux);

        $this->deleteEdlWithRelations($etatDesLieux);

        return redirect()
            ->route('etats-des-lieux.index')
            ->with('success', 'État des lieux supprimé.');
    }

    /**
     * Génération PDF
     */
    public function pdf(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        $etatDesLieux->load(['logement', 'user', 'pieces.elements.photos', 'compteurs', 'cles']);

        // Récupérer l'EDL d'entrée si c'est un EDL de sortie (pour comparaison)
        $edlEntree = null;
        if ($etatDesLieux->type === 'sortie') {
            $edlEntree = EtatDesLieux::with(['pieces.elements', 'compteurs', 'cles'])
                ->where('logement_id', $etatDesLieux->logement_id)
                ->where('type', 'entree')
                ->where('id', '!=', $etatDesLieux->id)
                ->orderBy('date_realisation', 'desc')
                ->first();
        }

        $pdf = Pdf::loadView('etats-des-lieux.pdf', compact('etatDesLieux', 'edlEntree'))
            ->setPaper('A4', 'portrait');

        $type = $etatDesLieux->type === 'entree' ? 'entree' : 'sortie';

        $filename = 'edl_' .
            $type . '_' .
            $etatDesLieux->logement->nom . '_' .
            $etatDesLieux->date_realisation->format('Y-m-d') .
            '.pdf';

        return $pdf->download($filename);
    }

    /**
     * API - Liste des typologies
     */
    public function getTypologies()
    {
        return response()->json(config('typologies'));
    }

    /**
     * API - Génération automatique des pièces
     */
    public function genererPieces(GenererPiecesRequest $request, EtatDesLieux $etatDesLieux)
    {
        $this->authorize('update', $etatDesLieux);

        $typologie = $request->validated('typologie');
        $typologies = config('typologies');

        if (!isset($typologies[$typologie])) {
            return response()->json([
                'success' => false,
                'error' => 'Typologie inconnue',
            ], 422);
        }

        if ($request->boolean('remplacer')) {
            $etatDesLieux->pieces()->delete();
        }

        $ordre = $etatDesLieux->pieces()->max('ordre') ?? 0;

        foreach ($typologies[$typologie]['pieces'] as $nomPiece) {
            $etatDesLieux->pieces()->create([
                'nom' => $nomPiece,
                'ordre' => ++$ordre,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($typologies[$typologie]['pieces']) . ' pièces générées avec succès',
            'redirect' => route('etats-des-lieux.edit', $etatDesLieux),
        ]);
    }
}
