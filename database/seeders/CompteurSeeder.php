<?php

namespace Database\Seeders;

use App\Models\Compteur;
use App\Models\EtatDesLieux;
use Illuminate\Database\Seeder;

class CompteurSeeder extends Seeder
{
    public function run(): void
    {
        $etatsDesLieux = EtatDesLieux::doesntHave('compteurs')->get();

        foreach ($etatsDesLieux as $edl) {
            // 70% de chance d'avoir des compteurs
            if (fake()->boolean(70)) {
                
                // Électricité (90% de chance)
                if (fake()->boolean(90)) {
                    Compteur::create([
                        'etat_des_lieux_id' => $edl->id,
                        'type' => 'electricite',
                        'numero' => fake()->numerify('##########'),
                        'index_value' => fake()->numberBetween(10000, 99999),
                        'commentaire' => fake()->boolean(20) ? 'Compteur dans le couloir' : null,
                    ]);
                }

                // Eau froide (80% de chance)
                if (fake()->boolean(80)) {
                    Compteur::create([
                        'etat_des_lieux_id' => $edl->id,
                        'type' => 'eau_froide',
                        'numero' => fake()->numerify('EF-########'),
                        'index_value' => fake()->numberBetween(100, 9999),
                        'commentaire' => null,
                    ]);
                }

                // Eau chaude (60% de chance)
                if (fake()->boolean(60)) {
                    Compteur::create([
                        'etat_des_lieux_id' => $edl->id,
                        'type' => 'eau_chaude',
                        'numero' => fake()->numerify('EC-########'),
                        'index_value' => fake()->numberBetween(50, 5000),
                        'commentaire' => null,
                    ]);
                }

                // Gaz (40% de chance)
                if (fake()->boolean(40)) {
                    Compteur::create([
                        'etat_des_lieux_id' => $edl->id,
                        'type' => 'gaz',
                        'numero' => fake()->numerify('GZ##########'),
                        'index_value' => fake()->numberBetween(1000, 50000),
                        'commentaire' => fake()->boolean(10) ? 'Compteur extérieur' : null,
                    ]);
                }
            }
        }
    }
}