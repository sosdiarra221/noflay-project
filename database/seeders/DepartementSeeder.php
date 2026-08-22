<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $departements = [
            'Direction',
            'RH',
            'Technique',
            'Commercial',
            'Finance',
            'Logistique',
        ];

        foreach ($departements as $nom) {
            Departement::updateOrCreate(['nom' => $nom]);
        }
    }
}
