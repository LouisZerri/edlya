<?php

namespace App\Http\Controllers;

use App\Http\Requests\PieceRequest;
use App\Models\EtatDesLieux;
use App\Models\Piece;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PieceController extends Controller
{
    public function store(PieceRequest $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

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
        $this->authorizeAccessPiece($piece);

        $piece->update($request->validated());

        return redirect()
            ->route('etats-des-lieux.edit', $piece->etat_des_lieux_id)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Pièce mise à jour.');
    }

    public function destroy(Piece $piece): RedirectResponse
    {
        $this->authorizeAccessPiece($piece);

        $etatDesLieuxId = $piece->etat_des_lieux_id;
        $piece->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('pieces')
            ->with('success', 'Pièce supprimée.');
    }

    private function authorizeAccess(EtatDesLieux $etatDesLieux): void
    {
        if ($etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }

    private function authorizeAccessPiece(Piece $piece): void
    {
        if ($piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}