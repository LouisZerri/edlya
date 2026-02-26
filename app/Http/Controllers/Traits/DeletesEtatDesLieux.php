<?php

namespace App\Http\Controllers\Traits;

use App\Models\EtatDesLieux;
use Illuminate\Support\Facades\Storage;

trait DeletesEtatDesLieux
{
    private function deleteEdlWithRelations(EtatDesLieux $edl): void
    {
        $edl->load(['pieces.elements.photos', 'compteurs', 'cles', 'partages']);

        // Supprimer les fichiers photos des éléments et des pièces
        foreach ($edl->pieces as $piece) {
            foreach ($piece->elements as $element) {
                foreach ($element->photos as $photo) {
                    $this->deleteEdlFile($photo->chemin);
                }
            }

            if (!empty($piece->photos)) {
                foreach ($piece->photos as $chemin) {
                    $this->deleteEdlFile($chemin);
                }
            }
        }

        // Supprimer les fichiers photos des compteurs (JSON array)
        foreach ($edl->compteurs as $compteur) {
            if (!empty($compteur->photos)) {
                foreach ($compteur->photos as $chemin) {
                    $this->deleteEdlFile($chemin);
                }
            }
        }

        // Supprimer les fichiers photos des clés
        foreach ($edl->cles as $cle) {
            if ($cle->photo) {
                $this->deleteEdlFile($cle->photo);
            }
        }

        // Suppression BDD en cascade
        foreach ($edl->pieces as $piece) {
            foreach ($piece->elements as $element) {
                $element->photos()->delete();
            }
            $piece->elements()->delete();
        }
        $edl->pieces()->delete();
        $edl->compteurs()->delete();
        $edl->cles()->delete();
        $edl->partages()->delete();
        $edl->delete();
    }

    private function deleteEdlFile(string $path): void
    {
        if (empty($path) || str_starts_with($path, '/uploads/')) {
            return;
        }

        Storage::delete($path);
    }
}
