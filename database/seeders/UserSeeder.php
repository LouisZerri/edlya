<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Edlya',
            'email' => 'admin@edlya.fr',
            'password' => 'password',
            'role' => 'admin',
            'entreprise' => 'GEST\'IMMO',
        ]);

        User::create([
            'name' => 'Jean Dupont',
            'email' => 'agent@edlya.fr',
            'password' => 'password',
            'role' => 'agent',
            'telephone' => '06 12 34 56 78',
            'entreprise' => 'GEST\'IMMO',
        ]);

        User::create([
            'name' => 'Marie Martin',
            'email' => 'bailleur@edlya.fr',
            'password' => 'password',
            'role' => 'bailleur',
            'telephone' => '06 98 76 54 32',
        ]);
    }
}