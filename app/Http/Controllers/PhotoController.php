<?php

namespace App\Http\Controllers;

use App\Models\Element;
use App\Models\Piece;
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

        // Ajouter automatiquement la référence photo dans les observations
        $this->addPhotoReferenceToObservations($element);

        return redirect()
            ->route('etats-des-lieux.edit', $element->piece->etat_des_lieux_id)
            ->withFragment('piece-' . $element->piece_id)
            ->with('success', 'Photo ajoutée.');
    }

    public function storeForPiece(Request $request, Piece $piece): RedirectResponse
    {
        // Vérifier que l'utilisateur a accès à cette pièce
        if ($piece->etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'element_id' => ['required', 'exists:elements,id'],
            'photo' => ['required', 'image', 'max:10240'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'photo.required' => 'La photo est requise.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
        ]);

        $element = Element::findOrFail($request->element_id);

        // Vérifier que l'élément appartient bien à cette pièce
        if ($element->piece_id !== $piece->id) {
            abort(403);
        }

        $path = $request->file('photo')->store('photos', 'public');

        Photo::create([
            'element_id' => $element->id,
            'chemin' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Ajouter automatiquement la référence photo dans les observations
        $this->addPhotoReferenceToObservations($element, $piece);

        return redirect()
            ->route('etats-des-lieux.edit', $piece->etat_des_lieux_id)
            ->withFragment('piece-' . $piece->id)
            ->with('success', 'Photo ajoutée.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $this->authorizeAccessPhoto($photo);

        $etatDesLieuxId = $photo->element->piece->etat_des_lieux_id;
        $pieceId = $photo->element->piece_id;
        $element = $photo->element;
        $piece = $element->piece;

        Storage::disk('public')->delete($photo->chemin);
        $photo->delete();

        // Recalculer les numéros de photos dans les observations
        $this->recalculatePhotoReferences($piece);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieuxId)
            ->withFragment('piece-' . $pieceId)
            ->with('success', 'Photo supprimée.');
    }

    /**
     * Ajoute automatiquement la référence "(Photo X)" dans les observations de l'élément
     */
    private function addPhotoReferenceToObservations(Element $element, ?Piece $piece = null): void
    {
        $piece = $piece ?? $element->piece;

        // Recharger pour avoir les photos à jour
        $piece->load('elements.photos');

        // Calculer le numéro TOTAL de photos dans la pièce
        // La nouvelle photo est la dernière, donc son numéro = total
        $totalPhotos = 0;
        foreach ($piece->elements as $el) {
            $totalPhotos += $el->photos->count();
        }

        // Le numéro de la nouvelle photo est le total (elle vient d'être ajoutée en dernier)
        $newPhotoNumber = $totalPhotos;

        // Ajouter la référence dans les observations
        $observations = $element->observations ?? '';
        $photoRef = "(Photo $newPhotoNumber)";

        // Vérifier si une référence photo existe déjà pour éviter les doublons
        if (!preg_match('/\(Photo \d+\)/', $observations)) {
            // Aucune référence, on ajoute
            $observations = trim($observations . ' ' . $photoRef);
        } else {
            // Des références existent, on ajoute la nouvelle
            $observations = trim($observations . ', ' . $photoRef);
        }

        $element->update(['observations' => trim($observations)]);
    }

    /**
     * Recalcule tous les numéros de photos après suppression
     */
    private function recalculatePhotoReferences(Piece $piece): void
    {
        $piece->load('elements.photos');

        $photoNumber = 0;
        foreach ($piece->elements as $element) {
            $photoRefs = [];
            foreach ($element->photos as $photo) {
                $photoNumber++;
                $photoRefs[] = "(Photo $photoNumber)";
            }

            // Retirer les anciennes références et ajouter les nouvelles
            $observations = $element->observations ?? '';
            // Supprimer toutes les références photo existantes
            $observations = preg_replace('/,?\s*\(Photo \d+\)/', '', $observations);
            $observations = trim($observations);

            if (!empty($photoRefs)) {
                $observations = trim($observations . ' ' . implode(', ', $photoRefs));
            }

            $element->update(['observations' => trim($observations) ?: null]);
        }
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
