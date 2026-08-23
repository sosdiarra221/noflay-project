<?php

namespace Database\Seeders;

use App\Models\ModePaiement;
use Illuminate\Database\Seeder;

class ModePaiementSeeder extends Seeder
{
    public function run(): void
    {
        $modes = [
            'Cash',
            'Wave',
            'Virement',
            'Chèque',
            'Orange Money',
        ];

        foreach ($modes as $nom) {
            ModePaiement::updateOrCreate(['nom' => $nom]);
        }
    }
}
