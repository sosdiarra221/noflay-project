<?php

namespace Database\Seeders;

use App\Models\Devise;
use App\Models\Reglage;
use Illuminate\Database\Seeder;

class ReglageSeeder extends Seeder
{
    public function run(): void
    {
        if (Reglage::query()->exists()) {
            return;
        }

        $deviseDefaut = Devise::where('est_defaut', true)->first() ?? Devise::first();

        Reglage::create([
            'nom_societe' => config('app.name', 'Mon Entreprise'),
            'email' => 'contact@example.com',
            'devise_id' => $deviseDefaut?->id,
            'smtp_type' => 'log',
            'smtp_from_address' => 'hello@example.com',
            'smtp_from_name' => config('app.name', 'Mon Entreprise'),
        ]);
    }
}
