<?php

namespace Database\Seeders;

use App\Models\Commercial\Source;
use Illuminate\Database\Seeder;

class CommercialSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            'Facebook', 'Internet', 'Contact', 'Site web', 'Appel', 'Email',
            'WhatsApp', 'Instagram', 'TikTok', 'Recommandation', 'Agence', 'Partenaire', 'Autre',
        ];

        foreach ($sources as $nom) {
            Source::updateOrCreate(['nom' => $nom]);
        }
    }
}
