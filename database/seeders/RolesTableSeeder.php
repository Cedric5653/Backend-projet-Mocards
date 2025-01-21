<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'role_id' => 1,
                'role_name' => 'Admin',
                'description' => 'Accès complet au système'
            ],
            [
                'role_id' => 2,
                'role_name' => 'Médecin',
                'description' => 'Accès aux dossiers des patients'
            ],
            [
                'role_id' => 3,
                'role_name' => 'Infirmier',
                'description' => 'Accès limité aux suivis médicaux'
            ],
            [
                'role_id' => 4,
                'role_name' => 'Secouriste',
                'description' => 'Accès uniquement aux cartes d\'urgence'
            ]
        ]);
    }
}