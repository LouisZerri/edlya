<?php

namespace Database\Seeders;

use App\Models\Cle;
use App\Models\EtatDesLieux;
use Illuminate\Database\Seeder;

class CleSeeder extends Seeder
{
    public function run(): void
    {
        $etatsDesLieux = EtatDesLieux::all();

        $typesCommuns = [
            ['type' => 'Porte d\'entrée', 'min' => 2, 'max' => 3],
            ['type' => 'Boîte aux lettres', 'min' => 1, 'max' => 2],
            ['type' => 'Cave', 'min' => 1, 'max' => 2],
            ['type' => 'Garage', 'min' => 1, 'max' => 2],
            ['type' => 'Parties communes', 'min' => 1, 'max' => 2],
            ['type' => 'Portail', 'min' => 1, 'max' => 2],
            ['type' => 'Local vélo', 'min' => 1, 'max' => 1],
        ];

        foreach ($etatsDesLieux as $edl) {
            // Toujours la porte d'entrée
            Cle::create([
                'etat_des_lieux_id' => $edl->id,
                'type' => 'Porte d\'entrée',
                'nombre' => fake()->numberBetween(2, 3),
                'commentaire' => fake()->boolean(20) ? 'Clés neuves' : null,
            ]);

            // Boîte aux lettres (90% de chance)
            if (fake()->boolean(90)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'Boîte aux lettres',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => null,
                ]);
            }

            // Parties communes (70% de chance)
            if (fake()->boolean(70)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'Parties communes',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => fake()->boolean(30) ? 'Badge magnétique' : null,
                ]);
            }

            // Cave (40% de chance)
            if (fake()->boolean(40)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'Cave',
                    'nombre' => 1,
                    'commentaire' => fake()->boolean(20) ? 'Cave n°' . fake()->numberBetween(1, 50) : null,
                ]);
            }

            // Garage (30% de chance)
            if (fake()->boolean(30)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'Garage',
                    'nombre' => fake()->numberBetween(1, 2),
                    'commentaire' => fake()->boolean(40) ? 'Télécommande portail' : null,
                ]);
            }

            // Digicode/Interphone (50% de chance)
            if (fake()->boolean(50)) {
                Cle::create([
                    'etat_des_lieux_id' => $edl->id,
                    'type' => 'Interphone/Digicode',
                    'nombre' => 1,
                    'commentaire' => 'Code : ' . fake()->numerify('####'),
                ]);
            }
        }
    }
}