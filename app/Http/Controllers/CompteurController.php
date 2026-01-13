<?php

namespace App\Http\Controllers;

use App\Models\Compteur;
use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompteurController extends Controller
{
    public function store(Request $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'type' => ['required', 'in:electricite,eau_froide,eau_chaude,gaz'],
            'numero' => ['nullable', 'string', 'max:255'],
            'index' => ['nullable', 'string', 'max:255'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ]);

        // Vérifier si ce type de compteur existe déjà
        $compteur = $etatDesLieux->compteurs()->where('type', $validated['type'])->first();

        if ($compteur) {
            // Mise à jour
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo
                if ($compteur->photo) {
                    Storage::disk('public')->delete($compteur->photo);
                }
                $validated['photo'] = $request->file('photo')->store('compteurs', 'public');
            }

            $compteur->update($validated);
            $message = 'Compteur mis à jour.';
        } else {
            // Création
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('compteurs', 'public');
            }

            $etatDesLieux->compteurs()->create($validated);
            $message = 'Compteur ajouté.';
        }

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', $message);
    }

    public function update(Request $request, Compteur $compteur): RedirectResponse
    {
        $etatDesLieux = $compteur->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'numero' => ['nullable', 'string', 'max:255'],
            'index' => ['nullable', 'string', 'max:255'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('photo')) {
            if ($compteur->photo) {
                Storage::disk('public')->delete($compteur->photo);
            }
            $validated['photo'] = $request->file('photo')->store('compteurs', 'public');
        }

        $compteur->update($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', 'Compteur mis à jour.');
    }

    public function destroy(Compteur $compteur): RedirectResponse
    {
        $etatDesLieux = $compteur->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        if ($compteur->photo) {
            Storage::disk('public')->delete($compteur->photo);
        }

        $compteur->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', 'Compteur supprimé.');
    }

    public function deletePhoto(Compteur $compteur): RedirectResponse
    {
        $etatDesLieux = $compteur->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        if ($compteur->photo) {
            Storage::disk('public')->delete($compteur->photo);
            $compteur->update(['photo' => null]);
        }

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', 'Photo supprimée.');
    }
}