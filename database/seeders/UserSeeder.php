<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    const MOT_DE_PASSE_PAR_DEFAUT = 'PWD@demo';
    const CODE_PIN_PAR_DEFAUT = '1234';

    /**
     * Rôle applicatif attribué par défaut à l'utilisateur démo de chaque département.
     */
    protected array $rolesParDepartement = [
        'Direction' => Role::ADMINISTRATEUR,
        'RH' => Role::ASSISTANT,
        'Technique' => Role::AGENT_IMMOBILIER,
        'Commercial' => Role::AGENT_IMMOBILIER,
        'Finance' => Role::COMPTABLE,
        'Logistique' => Role::ASSISTANT,
    ];

    public function run(): void
    {
        foreach (Departement::all() as $departement) {
            $nomRole = $this->rolesParDepartement[$departement->nom] ?? Role::ASSISTANT;
            $roleId = Role::where('nom', $nomRole)->value('id');

            User::updateOrCreate(
                ['email' => Str::slug($departement->nom).'@demo.com'],
                [
                    'name' => $departement->nom,
                    'identifiant' => Str::slug($departement->nom),
                    'password' => self::MOT_DE_PASSE_PAR_DEFAUT,
                    'code_pin' => self::CODE_PIN_PAR_DEFAUT,
                    'statut' => 'actif',
                    'role_id' => $roleId,
                    'departement_id' => $departement->id,
                ]
            );
        }
    }
}
