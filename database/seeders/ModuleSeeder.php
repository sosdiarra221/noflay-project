<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['cle' => 'locative', 'nom' => 'Module Locative', 'description' => "Gestion des bailleurs, biens, locataires, contrats de location, loyers, cautions et reversements.", 'icone' => 'bi-building', 'ordre' => 1],
            ['cle' => 'finance', 'nom' => 'Module Finance', 'description' => 'Suivi financier global : dépenses, cautions, reversements aux bailleurs, journal de caisse.', 'icone' => 'bi-cash-coin', 'ordre' => 2],
            ['cle' => 'commercial', 'nom' => 'Module Commercial', 'description' => 'Prospection, suivi des demandes clients, partenaires et conversion en location.', 'icone' => 'bi-bullseye', 'ordre' => 3],
            ['cle' => 'documents', 'nom' => 'Gestion Document', 'description' => 'Modèles de contrats et documents générés automatiquement pour les autres modules.', 'icone' => 'bi-file-earmark-text', 'ordre' => 4],
            ['cle' => 'facturation', 'nom' => 'Module Facturation', 'description' => 'Création de devis, gestion des clients/prospects et suivi de la facturation.', 'icone' => 'bi-receipt', 'ordre' => 5],
            ['cle' => 'administration', 'nom' => 'Direction & Administration', 'description' => 'Utilisateurs, rôles et permissions, sécurité, réglages généraux de la société.', 'icone' => 'bi-shield-lock', 'ordre' => 6],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(['cle' => $module['cle']], $module + ['actif' => true]);
        }
    }
}
