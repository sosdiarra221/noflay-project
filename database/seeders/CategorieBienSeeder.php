<?php

namespace Database\Seeders;

use App\Models\CategorieBien;
use Illuminate\Database\Seeder;

class CategorieBienSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Appartement',
            'Villa',
            'Maison',
            'Studio',
            'Chambre',
            'Bureau',
            'Boutique',
            'Local commercial',
            'Entrepôt',
            'Terrain',
            'Immeuble',
            'Hôtel',
            'Résidence',
            'Autre',
        ];

        foreach ($categories as $nom) {
            CategorieBien::updateOrCreate(['nom' => $nom]);
        }
    }
}
