<?php

namespace Database\Seeders;

use App\Models\CoutReparation;
use Illuminate\Database\Seeder;

class CoutReparationSeeder extends Seeder
{
    public function run(): void
    {
        $couts = [
            // SOLS
            [
                'type_element' => 'sol',
                'nom' => 'Ponçage et vitrification parquet',
                'description' => 'Ponçage, vitrification et finition',
                'unite' => 'm2',
                'prix_unitaire' => 35.00,
            ],
            [
                'type_element' => 'sol',
                'nom' => 'Remplacement lame parquet',
                'description' => 'Fourniture et pose d\'une lame',
                'unite' => 'unite',
                'prix_unitaire' => 45.00,
            ],
            [
                'type_element' => 'sol',
                'nom' => 'Remplacement moquette',
                'description' => 'Dépose, fourniture et pose moquette standard',
                'unite' => 'm2',
                'prix_unitaire' => 25.00,
            ],
            [
                'type_element' => 'sol',
                'nom' => 'Nettoyage moquette professionnel',
                'description' => 'Shampouinage et détachage',
                'unite' => 'm2',
                'prix_unitaire' => 8.00,
            ],
            [
                'type_element' => 'sol',
                'nom' => 'Remplacement carreau carrelage',
                'description' => 'Dépose, fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 55.00,
            ],
            [
                'type_element' => 'sol',
                'nom' => 'Réfection joints carrelage',
                'description' => 'Retrait et refaire joints',
                'unite' => 'm2',
                'prix_unitaire' => 15.00,
            ],

            // MURS
            [
                'type_element' => 'mur',
                'nom' => 'Peinture mur',
                'description' => 'Préparation, sous-couche et 2 couches finition',
                'unite' => 'm2',
                'prix_unitaire' => 18.00,
            ],
            [
                'type_element' => 'mur',
                'nom' => 'Rebouchage trou (petit)',
                'description' => 'Rebouchage et ponçage trou < 5cm',
                'unite' => 'unite',
                'prix_unitaire' => 8.00,
            ],
            [
                'type_element' => 'mur',
                'nom' => 'Rebouchage trou (gros)',
                'description' => 'Rebouchage et ponçage trou > 5cm',
                'unite' => 'unite',
                'prix_unitaire' => 25.00,
            ],
            [
                'type_element' => 'mur',
                'nom' => 'Remplacement papier peint',
                'description' => 'Dépose, préparation et pose',
                'unite' => 'm2',
                'prix_unitaire' => 22.00,
            ],
            [
                'type_element' => 'mur',
                'nom' => 'Traitement humidité',
                'description' => 'Diagnostic et traitement anti-humidité',
                'unite' => 'forfait',
                'prix_unitaire' => 150.00,
            ],

            // PLAFONDS
            [
                'type_element' => 'plafond',
                'nom' => 'Peinture plafond',
                'description' => 'Préparation et 2 couches',
                'unite' => 'm2',
                'prix_unitaire' => 22.00,
            ],
            [
                'type_element' => 'plafond',
                'nom' => 'Réparation fissure plafond',
                'description' => 'Ouverture, rebouchage et finition',
                'unite' => 'ml',
                'prix_unitaire' => 15.00,
            ],

            // MENUISERIES
            [
                'type_element' => 'menuiserie',
                'nom' => 'Remplacement poignée fenêtre',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 35.00,
            ],
            [
                'type_element' => 'menuiserie',
                'nom' => 'Remplacement joint fenêtre',
                'description' => 'Dépose ancien joint et pose nouveau',
                'unite' => 'ml',
                'prix_unitaire' => 12.00,
            ],
            [
                'type_element' => 'menuiserie',
                'nom' => 'Réglage/ajustement porte',
                'description' => 'Réglage gonds et serrure',
                'unite' => 'unite',
                'prix_unitaire' => 45.00,
            ],
            [
                'type_element' => 'menuiserie',
                'nom' => 'Remplacement porte intérieure',
                'description' => 'Fourniture et pose standard',
                'unite' => 'unite',
                'prix_unitaire' => 280.00,
            ],
            [
                'type_element' => 'menuiserie',
                'nom' => 'Réparation volet roulant',
                'description' => 'Diagnostic et réparation',
                'unite' => 'unite',
                'prix_unitaire' => 120.00,
            ],

            // ÉLECTRICITÉ
            [
                'type_element' => 'electricite',
                'nom' => 'Remplacement prise électrique',
                'description' => 'Fourniture et pose prise standard',
                'unite' => 'unite',
                'prix_unitaire' => 45.00,
            ],
            [
                'type_element' => 'electricite',
                'nom' => 'Remplacement interrupteur',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 40.00,
            ],
            [
                'type_element' => 'electricite',
                'nom' => 'Remplacement cache prise/interrupteur',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 12.00,
            ],

            // PLOMBERIE
            [
                'type_element' => 'plomberie',
                'nom' => 'Détartrage robinetterie',
                'description' => 'Démontage, détartrage, remontage',
                'unite' => 'unite',
                'prix_unitaire' => 35.00,
            ],
            [
                'type_element' => 'plomberie',
                'nom' => 'Remplacement mitigeur',
                'description' => 'Fourniture et pose mitigeur standard',
                'unite' => 'unite',
                'prix_unitaire' => 95.00,
            ],
            [
                'type_element' => 'plomberie',
                'nom' => 'Remplacement flexible douche',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 25.00,
            ],
            [
                'type_element' => 'plomberie',
                'nom' => 'Remplacement abattant WC',
                'description' => 'Fourniture et pose standard',
                'unite' => 'unite',
                'prix_unitaire' => 45.00,
            ],
            [
                'type_element' => 'plomberie',
                'nom' => 'Débouchage canalisation',
                'description' => 'Intervention débouchage',
                'unite' => 'forfait',
                'prix_unitaire' => 85.00,
            ],

            // CHAUFFAGE
            [
                'type_element' => 'chauffage',
                'nom' => 'Purge radiateur',
                'description' => 'Purge et vérification',
                'unite' => 'unite',
                'prix_unitaire' => 25.00,
            ],
            [
                'type_element' => 'chauffage',
                'nom' => 'Remplacement robinet radiateur',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 65.00,
            ],

            // ÉQUIPEMENTS
            [
                'type_element' => 'equipement',
                'nom' => 'Réparation placard (rail/porte)',
                'description' => 'Réglage ou remplacement rail',
                'unite' => 'unite',
                'prix_unitaire' => 55.00,
            ],
            [
                'type_element' => 'equipement',
                'nom' => 'Remplacement étagère',
                'description' => 'Fourniture et pose',
                'unite' => 'unite',
                'prix_unitaire' => 35.00,
            ],
            [
                'type_element' => 'equipement',
                'nom' => 'Nettoyage hotte aspirante',
                'description' => 'Dégraissage complet et remplacement filtre',
                'unite' => 'forfait',
                'prix_unitaire' => 45.00,
            ],
            [
                'type_element' => 'equipement',
                'nom' => 'Remplacement hotte aspirante',
                'description' => 'Fourniture et pose standard',
                'unite' => 'unite',
                'prix_unitaire' => 250.00,
            ],
            [
                'type_element' => 'equipement',
                'nom' => 'Remplacement plaque cuisson',
                'description' => 'Fourniture et pose standard',
                'unite' => 'unite',
                'prix_unitaire' => 350.00,
            ],
            [
                'type_element' => 'equipement',
                'nom' => 'Réparation plan de travail',
                'description' => 'Ponçage et traitement surface',
                'unite' => 'ml',
                'prix_unitaire' => 40.00,
            ],

            // NETTOYAGE GÉNÉRAL
            [
                'type_element' => 'autre',
                'nom' => 'Nettoyage fin de bail',
                'description' => 'Nettoyage complet du logement',
                'unite' => 'm2',
                'prix_unitaire' => 5.00,
            ],
            [
                'type_element' => 'autre',
                'nom' => 'Évacuation encombrants',
                'description' => 'Enlèvement et évacuation',
                'unite' => 'forfait',
                'prix_unitaire' => 150.00,
            ],
        ];

        foreach ($couts as $cout) {
            CoutReparation::firstOrCreate(
                ['type_element' => $cout['type_element'], 'nom' => $cout['nom']],
                $cout
            );
        }
    }
}