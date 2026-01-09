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

        Logement::create([
            'user_id' => $agent->id,
            'nom' => 'Appartement Belleville',
            'adresse' => '45 rue de Belleville',
            'code_postal' => '75020',
            'ville' => 'Paris',
            'type' => 'appartement',
            'surface' => 65.50,
            'nb_pieces' => 3,
            'description' => 'Bel appartement lumineux avec balcon',
        ]);

        Logement::create([
            'user_id' => $agent->id,
            'nom' => 'Studio Marais',
            'adresse' => '12 rue des Archives',
            'code_postal' => '75004',
            'ville' => 'Paris',
            'type' => 'studio',
            'surface' => 28.00,
            'nb_pieces' => 1,
            'description' => 'Studio rénové en plein cœur du Marais',
        ]);

        Logement::create([
            'user_id' => $bailleur->id,
            'nom' => 'Maison Vincennes',
            'adresse' => '8 avenue du Château',
            'code_postal' => '94300',
            'ville' => 'Vincennes',
            'type' => 'maison',
            'surface' => 120.00,
            'nb_pieces' => 5,
            'description' => 'Maison familiale avec jardin',
        ]);
    }
}