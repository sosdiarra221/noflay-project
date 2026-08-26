<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Provisionne une nouvelle société avec les données de référence essentielles (rôles,
 * permissions, catalogue de modules, paramétrage de base) — sans aucune donnée de démo
 * (utilisateur, employés fictifs...). Utilisé automatiquement à la création d'un tenant via
 * l'espace central (voir TenancyServiceProvider::events() / config/tenancy.php).
 *
 * DatabaseSeeder reste le seeder de développement local (avec données de démo) et n'est
 * jamais exécuté sur une société réelle.
 */
class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DeviseSeeder::class,
            DepartementSeeder::class,
            ReglageSeeder::class,
            CategorieBienSeeder::class,
            ModePaiementSeeder::class,
            CommercialSourceSeeder::class,
            CommercialTypeDemandeSeeder::class,
            DocumentTemplateSeeder::class,
            ModuleSeeder::class,
            TypeAbsenceSeeder::class,
            JourFerieSeeder::class,
        ]);
    }
}
