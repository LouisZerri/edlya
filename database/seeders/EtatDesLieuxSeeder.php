<?php

namespace Database\Seeders;

use App\Models\EtatDesLieux;
use App\Models\Element;
use App\Models\Logement;
use App\Models\Piece;
use Illuminate\Database\Seeder;

class EtatDesLieuxSeeder extends Seeder
{
    public function run(): void
    {
        $logement = Logement::first();

        // =====================================================
        // EDL D'ENTRÉE - Signé (état initial du logement)
        // =====================================================
        $edlEntree = EtatDesLieux::create([
            'logement_id' => $logement->id,
            'user_id' => $logement->user_id,
            'type' => 'entree',
            'date_realisation' => now()->subMonths(12),
            'locataire_nom' => 'Pierre Durand',
            'locataire_email' => 'pierre.durand@email.com',
            'locataire_telephone' => '06 11 22 33 44',
            'statut' => 'signe',
            'signature_bailleur' => 'data:image/png;base64,signature_bailleur_placeholder',
            'signature_locataire' => 'data:image/png;base64,signature_locataire_placeholder',
            'date_signature_bailleur' => now()->subMonths(12),
            'date_signature_locataire' => now()->subMonths(12),
            'observations_generales' => 'Logement en bon état général à l\'entrée du locataire.',
        ]);

        // Salon - Entrée
        $salonEntree = Piece::create([
            'etat_des_lieux_id' => $edlEntree->id,
            'nom' => 'Salon',
            'ordre' => 1,
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'sol',
            'nom' => 'Parquet chêne',
            'etat' => 'tres_bon',
            'observations' => 'Parquet vitrifié en excellent état',
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'mur',
            'nom' => 'Murs',
            'etat' => 'tres_bon',
            'observations' => 'Peinture blanche récente',
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'plafond',
            'nom' => 'Plafond',
            'etat' => 'tres_bon',
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'menuiserie',
            'nom' => 'Fenêtre double vitrage',
            'etat' => 'bon',
            'observations' => 'Fonctionne correctement',
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'chauffage',
            'nom' => 'Radiateur',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $salonEntree->id,
            'type' => 'electricite',
            'nom' => 'Prises électriques (x4)',
            'etat' => 'bon',
        ]);

        // Chambre - Entrée
        $chambreEntree = Piece::create([
            'etat_des_lieux_id' => $edlEntree->id,
            'nom' => 'Chambre',
            'ordre' => 2,
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'sol',
            'nom' => 'Moquette',
            'etat' => 'bon',
            'observations' => 'Moquette beige propre',
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'mur',
            'nom' => 'Murs',
            'etat' => 'tres_bon',
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'plafond',
            'nom' => 'Plafond',
            'etat' => 'tres_bon',
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'menuiserie',
            'nom' => 'Fenêtre double vitrage',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'menuiserie',
            'nom' => 'Porte',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $chambreEntree->id,
            'type' => 'equipement',
            'nom' => 'Placard intégré',
            'etat' => 'tres_bon',
            'observations' => 'Portes coulissantes fonctionnelles',
        ]);

        // Cuisine - Entrée
        $cuisineEntree = Piece::create([
            'etat_des_lieux_id' => $edlEntree->id,
            'nom' => 'Cuisine',
            'ordre' => 3,
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'sol',
            'nom' => 'Carrelage',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'mur',
            'nom' => 'Murs + crédence',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'equipement',
            'nom' => 'Plan de travail',
            'etat' => 'tres_bon',
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'equipement',
            'nom' => 'Évier inox',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'equipement',
            'nom' => 'Plaques de cuisson',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $cuisineEntree->id,
            'type' => 'equipement',
            'nom' => 'Hotte aspirante',
            'etat' => 'bon',
        ]);

        // Salle de bain - Entrée
        $sdbEntree = Piece::create([
            'etat_des_lieux_id' => $edlEntree->id,
            'nom' => 'Salle de bain',
            'ordre' => 4,
        ]);

        Element::create([
            'piece_id' => $sdbEntree->id,
            'type' => 'sol',
            'nom' => 'Carrelage',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $sdbEntree->id,
            'type' => 'mur',
            'nom' => 'Faïence murale',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $sdbEntree->id,
            'type' => 'plomberie',
            'nom' => 'Lavabo',
            'etat' => 'bon',
        ]);

        Element::create([
            'piece_id' => $sdbEntree->id,
            'type' => 'plomberie',
            'nom' => 'Douche',
            'etat' => 'tres_bon',
            'observations' => 'Paroi de douche neuve',
        ]);

        Element::create([
            'piece_id' => $sdbEntree->id,
            'type' => 'plomberie',
            'nom' => 'WC',
            'etat' => 'bon',
        ]);

        // =====================================================
        // EDL DE SORTIE - Avec dégradations
        // =====================================================
        $edlSortie = EtatDesLieux::create([
            'logement_id' => $logement->id,
            'user_id' => $logement->user_id,
            'type' => 'sortie',
            'date_realisation' => now(),
            'locataire_nom' => 'Pierre Durand',
            'locataire_email' => 'pierre.durand@email.com',
            'locataire_telephone' => '06 11 22 33 44',
            'statut' => 'termine',
            'observations_generales' => 'Quelques dégradations constatées après 12 mois d\'occupation.',
        ]);

        // Salon - Sortie (avec dégradations)
        $salonSortie = Piece::create([
            'etat_des_lieux_id' => $edlSortie->id,
            'nom' => 'Salon',
            'ordre' => 1,
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'sol',
            'nom' => 'Parquet chêne',
            'etat' => 'usage', // Dégradé : tres_bon -> usage
            'observations' => 'Rayures visibles près du canapé, une latte légèrement décollée',
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'mur',
            'nom' => 'Murs',
            'etat' => 'usage', // Dégradé : tres_bon -> usage
            'observations' => 'Traces de meubles, 2 trous de chevilles non rebouchés',
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'plafond',
            'nom' => 'Plafond',
            'etat' => 'bon', // Légèrement dégradé : tres_bon -> bon
            'observations' => 'Légère trace d\'humidité dans un angle',
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'menuiserie',
            'nom' => 'Fenêtre double vitrage',
            'etat' => 'bon', // Identique
            'observations' => 'Fonctionne correctement',
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'chauffage',
            'nom' => 'Radiateur',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $salonSortie->id,
            'type' => 'electricite',
            'nom' => 'Prises électriques (x4)',
            'etat' => 'mauvais', // Dégradé : bon -> mauvais
            'observations' => '1 prise ne fonctionne plus, cache cassé sur une autre',
        ]);

        // Chambre - Sortie (avec dégradations)
        $chambreSortie = Piece::create([
            'etat_des_lieux_id' => $edlSortie->id,
            'nom' => 'Chambre',
            'ordre' => 2,
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'sol',
            'nom' => 'Moquette',
            'etat' => 'mauvais', // Dégradé : bon -> mauvais
            'observations' => 'Taches importantes, brûlure de cigarette visible',
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'mur',
            'nom' => 'Murs',
            'etat' => 'bon', // Légèrement dégradé : tres_bon -> bon
            'observations' => 'Quelques traces, peinture légèrement jaunie',
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'plafond',
            'nom' => 'Plafond',
            'etat' => 'tres_bon', // Identique
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'menuiserie',
            'nom' => 'Fenêtre double vitrage',
            'etat' => 'usage', // Dégradé : bon -> usage
            'observations' => 'Poignée difficile à tourner, joint usé',
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'menuiserie',
            'nom' => 'Porte',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $chambreSortie->id,
            'type' => 'equipement',
            'nom' => 'Placard intégré',
            'etat' => 'usage', // Dégradé : tres_bon -> usage
            'observations' => 'Rail de porte coulissante grippé, étagère fissurée',
        ]);

        // Cuisine - Sortie (avec dégradations)
        $cuisineSortie = Piece::create([
            'etat_des_lieux_id' => $edlSortie->id,
            'nom' => 'Cuisine',
            'ordre' => 3,
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'sol',
            'nom' => 'Carrelage',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'mur',
            'nom' => 'Murs + crédence',
            'etat' => 'usage', // Dégradé : bon -> usage
            'observations' => 'Traces de graisse sur crédence, peinture écaillée près de l\'évier',
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'equipement',
            'nom' => 'Plan de travail',
            'etat' => 'usage', // Dégradé : tres_bon -> usage
            'observations' => 'Traces de brûlures, rayures visibles',
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'equipement',
            'nom' => 'Évier inox',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'equipement',
            'nom' => 'Plaques de cuisson',
            'etat' => 'usage', // Dégradé : bon -> usage
            'observations' => '1 plaque ne chauffe plus correctement',
        ]);

        Element::create([
            'piece_id' => $cuisineSortie->id,
            'type' => 'equipement',
            'nom' => 'Hotte aspirante',
            'etat' => 'mauvais', // Dégradé : bon -> mauvais
            'observations' => 'Ne fonctionne plus, filtre très encrassé',
        ]);

        // Salle de bain - Sortie (avec dégradations)
        $sdbSortie = Piece::create([
            'etat_des_lieux_id' => $edlSortie->id,
            'nom' => 'Salle de bain',
            'ordre' => 4,
        ]);

        Element::create([
            'piece_id' => $sdbSortie->id,
            'type' => 'sol',
            'nom' => 'Carrelage',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $sdbSortie->id,
            'type' => 'mur',
            'nom' => 'Faïence murale',
            'etat' => 'usage', // Dégradé : bon -> usage
            'observations' => 'Joints noircis, 1 carreau fêlé',
        ]);

        Element::create([
            'piece_id' => $sdbSortie->id,
            'type' => 'plomberie',
            'nom' => 'Lavabo',
            'etat' => 'bon', // Identique
        ]);

        Element::create([
            'piece_id' => $sdbSortie->id,
            'type' => 'plomberie',
            'nom' => 'Douche',
            'etat' => 'usage', // Dégradé : tres_bon -> usage
            'observations' => 'Calcaire important sur paroi, pommeau entartré',
        ]);

        Element::create([
            'piece_id' => $sdbSortie->id,
            'type' => 'plomberie',
            'nom' => 'WC',
            'etat' => 'bon', // Identique
        ]);
    }
}