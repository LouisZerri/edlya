<?php

namespace App\Http\Controllers;

use App\Models\Cle;
use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CleController extends Controller
{
    public function store(Request $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'nombre' => ['required', 'integer', 'min:1', 'max:99'],
            'commentaire' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        // Gérer l'upload de photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('cles', 'public');
        }

        $etatDesLieux->cles()->create($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Clé ajoutée.');
    }

    public function update(Request $request, Cle $cle): RedirectResponse
    {
        $etatDesLieux = $cle->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'nombre' => ['required', 'integer', 'min:1', 'max:99'],
            'commentaire' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        // Gérer l'upload de photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($cle->photo) {
                Storage::disk('public')->delete($cle->photo);
            }
            $validated['photo'] = $request->file('photo')->store('cles', 'public');
        }

        $cle->update($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Clé mise à jour.');
    }

    public function destroy(Cle $cle): RedirectResponse
    {
        $etatDesLieux = $cle->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        // Supprimer la photo
        if ($cle->photo) {
            Storage::disk('public')->delete($cle->photo);
        }

        $cle->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Clé supprimée.');
    }

    public function deletePhoto(Cle $cle): RedirectResponse
    {
        $etatDesLieux = $cle->etatDesLieux;
        $this->authorize('update', $etatDesLieux);

        if ($cle->photo) {
            Storage::disk('public')->delete($cle->photo);
            $cle->update(['photo' => null]);
        }

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Photo supprimée.');
    }
}