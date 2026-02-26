<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@edlya.fr'],
            [
                'name' => 'Admin Edlya',
                'password' => 'password',
                'role' => 'admin',
                'entreprise' => 'GEST\'IMMO',
            ]
        );

        User::firstOrCreate(
            ['email' => 'agent@edlya.fr'],
            [
                'name' => 'Jean Dupont',
                'password' => 'password',
                'role' => 'agent',
                'telephone' => '06 12 34 56 78',
                'entreprise' => 'GEST\'IMMO',
            ]
        );

        User::firstOrCreate(
            ['email' => 'bailleur@edlya.fr'],
            [
                'name' => 'Marie Martin',
                'password' => 'password',
                'role' => 'bailleur',
                'telephone' => '06 98 76 54 32',
            ]
        );
    }
}