<?php

namespace App\Http\Controllers;

use App\Http\Requests\PieceRequest;
use App\Models\EtatDesLieux;
use App\Models\Piece;
use Illuminate\Http\RedirectResponse;

class PieceController extends Controller
{
    public function store(PieceRequest $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorize('update', $etatDesLieux);

        $ordre = $etatDesLieux->pieces()->max('ordre') + 1;

        $piece = $etatDesLieux->pieces()->create([
            ...$request->validated(),
            'ordre' => $ordre,
        ]);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Pièce ajoutée.');
    }

    public function update(PieceRequest $request, Piece $piece): RedirectResponse
    {
        $this->authorize('update', $piece);

        $piece->update($request->validated());

        return redirect()
            ->route('etats-des-lieux.edit', $piece->etat_des_lieux_id)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Pièce mise à jour.');
    }

    public function destroy(Piece $piece): RedirectResponse
    {
        $this->authorize('delete', $piece);

        $etatDesLieuxId = $piece->etat_des_lieux_id;
        $piece->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('pieces')
            ->with('success', 'Pièce supprimée.');
    }
}