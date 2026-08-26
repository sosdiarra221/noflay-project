<?php

namespace Database\Seeders;

use App\Models\Rh\JourFerie;
use Illuminate\Database\Seeder;

class JourFerieSeeder extends Seeder
{
    public function run(): void
    {
        // Uniquement des fériés à date fixe — les fêtes mobiles (religieuses) doivent être
        // ressaisies chaque année et ne sont donc pas marquées récurrentes ici.
        $joursFeries = [
            ['nom' => "Jour de l'An", 'date' => '2026-01-01'],
            ['nom' => 'Fête du Travail', 'date' => '2026-05-01'],
            ['nom' => "Fête de l'Indépendance", 'date' => '2026-04-04'],
            ['nom' => 'Noël', 'date' => '2026-12-25'],
        ];

        foreach ($joursFeries as $ferie) {
            JourFerie::firstOrCreate(
                ['nom' => $ferie['nom']],
                ['date' => $ferie['date'], 'recurrent_annuel' => true]
            );
        }
    }
}
