<?php

namespace Database\Seeders;

use App\Models\Devise;
use Illuminate\Database\Seeder;

class DeviseSeeder extends Seeder
{
    public function run(): void
    {
        $devises = [
            ['code' => 'XOF', 'nom' => 'Franc CFA', 'symbole' => 'FCFA', 'est_defaut' => true],
            ['code' => 'EUR', 'nom' => 'Euro', 'symbole' => 'EURO', 'est_defaut' => false],
            ['code' => 'USD', 'nom' => 'Dollar Américain', 'symbole' => 'DOLLAR', 'est_defaut' => false],
        ];

        foreach ($devises as $devise) {
            Devise::updateOrCreate(['code' => $devise['code']], $devise);
        }
    }
}
