<?php

namespace Database\Seeders;

use App\Models\Rh\TypeAbsence;
use Illuminate\Database\Seeder;

class TypeAbsenceSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Congé Annuel',
            'Congé Maladie',
            'Congé Maternité',
            "Autorisation d'absence",
            'Permission',
        ];

        foreach ($types as $nom) {
            TypeAbsence::firstOrCreate(['nom' => $nom], ['actif' => true, 'est_urgence' => false]);
        }

        TypeAbsence::firstOrCreate(['nom' => 'Urgence'], ['actif' => true, 'est_urgence' => true]);
    }
}
