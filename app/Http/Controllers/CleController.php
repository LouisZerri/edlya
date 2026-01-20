<?php

namespace App\Http\Controllers;

use App\Http\Requests\CleRequest;
use App\Models\Cle;
use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class CleController extends Controller
{
    public function store(CleRequest $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorize('update', $etatDesLieux);

        $validated = $request->validated();

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

    public function update(CleRequest $request, Cle $cle): RedirectResponse
    {
        $this->authorize('update', $cle);

        $validated = $request->validated();

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
            ->route('etats-des-lieux.edit', $cle->etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Clé mise à jour.');
    }

    public function destroy(Cle $cle): RedirectResponse
    {
        $this->authorize('delete', $cle);

        $etatDesLieux = $cle->etatDesLieux;

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
        $this->authorize('update', $cle);

        if ($cle->photo) {
            Storage::disk('public')->delete($cle->photo);
            $cle->update(['photo' => null]);
        }

        return redirect()
            ->route('etats-des-lieux.edit', $cle->etatDesLieux)
            ->withFragment('cles')
            ->with('success', 'Photo supprimée.');
    }
}