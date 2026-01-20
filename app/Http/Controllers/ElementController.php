<?php

namespace App\Http\Controllers;

use App\Http\Requests\ElementRequest;
use App\Models\Element;
use App\Models\Piece;
use Illuminate\Http\RedirectResponse;

class ElementController extends Controller
{
    public function store(ElementRequest $request, Piece $piece): RedirectResponse
    {
        $this->authorize('update', $piece);

        $piece->elements()->create($request->validated());

        return redirect()
            ->route('etats-des-lieux.edit', $piece->etat_des_lieux_id)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Élément ajouté.');
    }

    public function update(ElementRequest $request, Element $element): RedirectResponse
    {
        $this->authorize('update', $element);

        $validated = $request->validated();

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
        $this->authorize('delete', $element);

        $etatDesLieuxId = $element->piece->etat_des_lieux_id;
        $pieceId = $element->piece_id;
        $element->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('piece-' . $pieceId)
            ->with('success', 'Élément supprimé.');
    }
}