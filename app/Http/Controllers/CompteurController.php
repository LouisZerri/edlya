<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompteurRequest;
use App\Models\Compteur;
use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class CompteurController extends Controller
{
    public function store(CompteurRequest $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validated();

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

    public function update(CompteurRequest $request, Compteur $compteur): RedirectResponse
    {
        $this->authorize('update', $compteur);

        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($compteur->photo) {
                Storage::disk('public')->delete($compteur->photo);
            }
            $validated['photo'] = $request->file('photo')->store('compteurs', 'public');
        }

        $compteur->update($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $compteur->etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', 'Compteur mis à jour.');
    }

    public function destroy(Compteur $compteur): RedirectResponse
    {
        $this->authorize('delete', $compteur);

        $etatDesLieux = $compteur->etatDesLieux;

        if ($compteur->photo) {
            Storage::disk('public')->delete($compteur->photo);
        }

        $compteur->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('compteurs')
            ->with('success', 'Compteur supprimé.');
    }

    public function deletePhoto(Compteur $compteur, int $index = 0)
    {
        $this->authorize('update', $compteur);

        if ($compteur->photos && isset($compteur->photos[$index])) {
            $photos = $compteur->photos;
            
            // Supprimer le fichier
            Storage::disk('public')->delete($photos[$index]);
            
            // Retirer du tableau
            array_splice($photos, $index, 1);
            
            // Mettre à jour (null si vide)
            $compteur->update([
                'photos' => !empty($photos) ? array_values($photos) : null,
            ]);
        }

        return back()->with('success', 'Photo supprimée');
    }
}