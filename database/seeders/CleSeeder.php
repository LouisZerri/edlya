<?php

namespace Database\Seeders;

use App\Models\Cle;
use App\Models\EtatDesLieux;
use Illuminate\Database\Seeder;

class CleSeeder extends Seeder
{
    public function run(): void
    {
        $etatsDesLieux = EtatDesLieux::doesntHave('cles')->get();

        foreach ($etatsDesLieux as $edl) {
            // Toujours la porte d'entrée
            Cle::create([
                'etat_des_lieux_id' => $edl->id,
                'type' => 'porte_entree',
                'nombre' => fake()->numberBetween(2, 3),
                'commentaire' => fake()->boolean(20) ? 'Clés neuves' : null,
            ]);

            // Boîte aux lettres (90% de chance)
            if (fake()->boolean(90)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'boite_lettres',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => null,
                ]);
            }

            // Parties communes (70% de chance)
            if (fake()->boolean(70)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'parties_communes',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => fake()->boolean(30) ? 'Badge magnétique' : null,
                ]);
            }

            // Cave (40% de chance)
            if (fake()->boolean(40)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'cave',
                    'nombre' => 1,
                    'commentaire' => fake()->boolean(20) ? 'Cave n°' . fake()->numberBetween(1, 50) : null,
                ]);
            }

            // Garage (30% de chance)
            if (fake()->boolean(30)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'garage',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => fake()->boolean(40) ? 'Télécommande portail' : null,
                ]);
            }

            // Digicode (50% de chance)
            if (fake()->boolean(50)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'digicode',
                    'nombre' => 1,
                    'commentaire' => 'Code : ' . fake()->numerify('####'),
                ]);
            }
        }
    }
}
