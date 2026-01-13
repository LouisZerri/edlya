<?php

namespace App\Http\Controllers;

use App\Models\Element;
use App\Models\Piece;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementController extends Controller
{
    public function store(Request $request, Piece $piece): RedirectResponse
    {
        $this->authorizeAccess($piece);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:255'],
            'etat' => ['required', 'in:neuf,tres_bon,bon,usage,mauvais,hors_service'],
            'observations' => ['nullable', 'string', 'max:1000'],
            'degradations' => ['nullable', 'array'],
            'degradations.*' => ['string', 'max:100'],
        ]);

        $piece->elements()->create($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $piece->etat_des_lieux_id)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Élément ajouté.');
    }

    public function update(Request $request, Element $element): RedirectResponse
    {
        $this->authorizeAccessElement($element);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:255'],
            'etat' => ['required', 'in:neuf,tres_bon,bon,usage,mauvais,hors_service'],
            'observations' => ['nullable', 'string', 'max:1000'],
            'degradations' => ['nullable', 'array'],
            'degradations.*' => ['string', 'max:100'],
        ]);

        // Si aucune dégradation cochée, mettre un tableau vide
        if (!isset($validated['degradations'])) {
            $validated['degradations'] = [];
        }

        $element->update($validated);

        return redirect()
            ->route('etats-des-lieux.edit', $element->piece->etat_des_lieux_id)
            ->withFragment('piece-' . $element->piece_id)
            ->with('success', 'Élément mis à jour.');
    }

    public function destroy(Element $element): RedirectResponse
    {
        $this->authorizeAccessElement($element);

        $etatDesLieuxId = $element->piece->etat_des_lieux_id;
        $pieceId = $element->piece_id;
        $element->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('piece-' . $pieceId)
            ->with('success', 'Élément supprimé.');
    }

    private function authorizeAccess(Piece $piece): void
    {
        if ($piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }

    private function authorizeAccessElement(Element $element): void
    {
        if ($element->piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}