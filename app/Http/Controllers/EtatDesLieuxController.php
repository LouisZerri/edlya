<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Models\Logement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatDesLieuxController extends Controller
{
    public function index()
    {
        $etatsDesLieux = EtatDesLieux::with('logement')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('etats-des-lieux.index', compact('etatsDesLieux'));
    }

    public function create()
    {
        $logements = Logement::where('user_id', Auth::id())->get();

        return view('etats-des-lieux.create', [
            'logements' => $logements,
            'logementSelectionne' => request('logement_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'logement_id' => ['required', 'exists:logements,id'],
            'type' => ['required', 'in:entree,sortie'],
            'date_realisation' => ['required', 'date'],
            'locataire_nom' => ['required', 'string', 'max:255'],
            'locataire_email' => ['nullable', 'email', 'max:255'],
            'locataire_telephone' => ['nullable', 'string', 'max:20'],
            'observations_generales' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['statut'] = 'brouillon';

        $etatDesLieux = EtatDesLieux::create($validated);

        return redirect()->route('etats-des-lieux.edit', $etatDesLieux)
            ->with('success', 'État des lieux créé avec succès.');
    }

    public function show(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos']);

        return view('etats-des-lieux.show', compact('etatDesLieux'));
    }

    public function edit(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('update', $etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos']);
        $logements = Logement::where('user_id', Auth::id())->get();

        return view('etats-des-lieux.edit', compact('etatDesLieux', 'logements'));
    }

    public function update(Request $request, EtatDesLieux $etatDesLieux)
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'logement_id' => ['required', 'exists:logements,id'],
            'type' => ['required', 'in:entree,sortie'],
            'date_realisation' => ['required', 'date'],
            'locataire_nom' => ['required', 'string', 'max:255'],
            'locataire_email' => ['nullable', 'email', 'max:255'],
            'locataire_telephone' => ['nullable', 'string', 'max:20'],
            'observations_generales' => ['nullable', 'string'],
            'statut' => ['nullable', 'in:brouillon,en_cours,termine'],
        ]);

        $etatDesLieux->update($validated);

        return redirect()->route('etats-des-lieux.edit', $etatDesLieux)
            ->with('success', 'État des lieux mis à jour.');
    }

    public function destroy(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('delete', $etatDesLieux);

        $etatDesLieux->delete();

        return redirect()->route('etats-des-lieux.index')
            ->with('success', 'État des lieux supprimé.');
    }

    public function pdf(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        $etatDesLieux->load(['logement', 'user', 'pieces.elements.photos']);

        $pdf = Pdf::loadView('etats-des-lieux.pdf', compact('etatDesLieux'));
        $pdf->setPaper('A4', 'portrait');

        $type = $etatDesLieux->type === 'entree' ? 'entree' : 'sortie';
        $filename = 'edl_' . $type . '_' . $etatDesLieux->logement->nom . '_' . $etatDesLieux->date_realisation->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}