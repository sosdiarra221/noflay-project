<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [Role::ADMINISTRATEUR, 'Administrateur'],
            [Role::DIRECTEUR, 'Directeur / Responsable agence'],
            [Role::AGENT_IMMOBILIER, 'Agent immobilier'],
            [Role::COMPTABLE, 'Comptable'],
            [Role::ASSISTANT, 'Assistant'],
        ];

        foreach ($roles as [$nom, $libelle]) {
            Role::updateOrCreate(['nom' => $nom], ['libelle' => $libelle]);
        }
    }
}
