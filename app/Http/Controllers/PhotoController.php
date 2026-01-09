<?php

namespace App\Http\Controllers;

use App\Models\Element;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request, Element $element): RedirectResponse
    {
        $this->authorizeAccess($element);

        $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
            'legende' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'photo.required' => 'La photo est requise.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        $element->photos()->create([
            'chemin' => $path,
            'legende' => $request->legende,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()
            ->route('etats-des-lieux.edit', $element->piece->etat_des_lieux_id)
            ->withFragment('piece-' . $element->piece_id)
            ->with('success', 'Photo ajoutée.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $this->authorizeAccessPhoto($photo);

        $etatDesLieuxId = $photo->element->piece->etat_des_lieux_id;
        $pieceId = $photo->element->piece_id;

        Storage::disk('public')->delete($photo->chemin);
        $photo->delete();

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('piece-' . $pieceId)
            ->with('success', 'Photo supprimée.');
    }

    private function authorizeAccess(Element $element): void
    {
        if ($element->piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }

    private function authorizeAccessPhoto(Photo $photo): void
    {
        if ($photo->element->piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}