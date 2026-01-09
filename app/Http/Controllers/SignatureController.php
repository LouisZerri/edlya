<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SignatureController extends Controller
{
    public function show(EtatDesLieux $etatDesLieux): View
    {
        $this->authorizeAccess($etatDesLieux);

        return view('etats-des-lieux.signature', compact('etatDesLieux'));
    }

    public function store(Request $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

        $request->validate([
            'signature_bailleur' => ['required', 'string'],
            'signature_locataire' => ['required', 'string'],
        ], [
            'signature_bailleur.required' => 'La signature du bailleur est requise.',
            'signature_locataire.required' => 'La signature du locataire est requise.',
        ]);

        $etatDesLieux->update([
            'signature_bailleur' => $request->signature_bailleur,
            'signature_locataire' => $request->signature_locataire,
            'date_signature_bailleur' => now(),
            'date_signature_locataire' => now(),
            'statut' => 'signe',
        ]);

        return redirect()
            ->route('etats-des-lieux.show', $etatDesLieux)
            ->with('success', 'État des lieux signé avec succès.');
    }

    private function authorizeAccess(EtatDesLieux $etatDesLieux): void
    {
        if ($etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}