<?php

namespace Database\Seeders;

use App\Models\Commercial\TypeDemande;
use Illuminate\Database\Seeder;

class CommercialTypeDemandeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Vente', 'Achat', 'Location', 'Gestion immobilière',
            'Estimation', 'Terrain', 'Local commercial', 'Investissement', 'Autre',
        ];

        foreach ($types as $nom) {
            TypeDemande::updateOrCreate(['nom' => $nom]);
        }
    }
}
