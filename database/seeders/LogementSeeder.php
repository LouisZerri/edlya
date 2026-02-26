<?php

namespace Database\Seeders;

use App\Models\Logement;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogementSeeder extends Seeder
{
    public function run(): void
    {
        $agent = User::where('email', 'agent@edlya.fr')->first();
        $bailleur = User::where('email', 'bailleur@edlya.fr')->first();

        if (!$agent || !$bailleur) {
            return;
        }

        Logement::firstOrCreate(
            ['user_id' => $agent->id, 'adresse' => '45 rue de Belleville'],
            [
                'nom' => 'Appartement Belleville',
                'code_postal' => '75020',
                'ville' => 'Paris',
                'type' => 'appartement',
                'surface' => 65.50,
                'nb_pieces' => 3,
                'description' => 'Bel appartement lumineux avec balcon',
            ]
        );

        Logement::firstOrCreate(
            ['user_id' => $agent->id, 'adresse' => '12 rue des Archives'],
            [
                'nom' => 'Studio Marais',
                'code_postal' => '75004',
                'ville' => 'Paris',
                'type' => 'studio',
                'surface' => 28.00,
                'nb_pieces' => 1,
                'description' => 'Studio rénové en plein cœur du Marais',
            ]
        );

        Logement::firstOrCreate(
            ['user_id' => $bailleur->id, 'adresse' => '8 avenue du Château'],
            [
                'nom' => 'Maison Vincennes',
                'code_postal' => '94300',
                'ville' => 'Vincennes',
                'type' => 'maison',
                'surface' => 120.00,
                'nb_pieces' => 5,
                'description' => 'Maison familiale avec jardin',
            ]
        );
    }
}