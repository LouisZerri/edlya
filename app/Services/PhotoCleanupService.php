<?php

namespace App\Services;

use App\Models\Cle;
use App\Models\Compteur;
use App\Models\Element;
use App\Models\EtatDesLieux;
use App\Models\Logement;
use App\Models\Photo;
use App\Models\Piece;
use Illuminate\Support\Facades\Storage;

class PhotoCleanupService
{
    /**
     * Supprimer toutes les photos associées à un état des lieux
     */
    public function cleanupEtatDesLieux(EtatDesLieux $etatDesLieux): void
    {
        // Photos des éléments
        foreach ($etatDesLieux->pieces as $piece) {
            $this->cleanupPiece($piece);
        }

        // Photos des compteurs
        foreach ($etatDesLieux->compteurs as $compteur) {
            $this->cleanupCompteur($compteur);
        }

        // Photos des clés
        foreach ($etatDesLieux->cles as $cle) {
            $this->cleanupCle($cle);
        }
    }

    /**
     * Supprimer toutes les photos associées à un logement
     */
    public function cleanupLogement(Logement $logement): void
    {
        foreach ($logement->etatsDesLieux as $etatDesLieux) {
            $this->cleanupEtatDesLieux($etatDesLieux);
        }
    }

    /**
     * Supprimer toutes les photos associées à une pièce
     */
    public function cleanupPiece(Piece $piece): void
    {
        foreach ($piece->elements as $element) {
            $this->cleanupElement($element);
        }
    }

    /**
     * Supprimer toutes les photos associées à un élément
     */
    public function cleanupElement(Element $element): void
    {
        foreach ($element->photos as $photo) {
            $this->cleanupPhoto($photo);
        }
    }

    /**
     * Supprimer une photo du stockage
     */
    public function cleanupPhoto(Photo $photo): void
    {
        if ($photo->chemin) {
            Storage::disk('public')->delete($photo->chemin);
        }
    }

    /**
     * Supprimer les photos d'un compteur
     */
    public function cleanupCompteur(Compteur $compteur): void
    {
        if ($compteur->photos) {
            foreach ($compteur->photos as $photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
        }
    }

    /**
     * Supprimer la photo d'une clé
     */
    public function cleanupCle(Cle $cle): void
    {
        if ($cle->photo) {
            Storage::disk('public')->delete($cle->photo);
        }
    }
}
