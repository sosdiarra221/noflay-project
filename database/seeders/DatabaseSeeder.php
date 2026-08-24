<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'role_id' => Role::where('nom', Role::ADMINISTRATEUR)->value('id'),
            ]
        );

        $this->call([
            DeviseSeeder::class,
            DepartementSeeder::class,
            ReglageSeeder::class,
            CategorieBienSeeder::class,
            ModePaiementSeeder::class,
            CommercialSourceSeeder::class,
            CommercialTypeDemandeSeeder::class,
            UserSeeder::class,
            DocumentTemplateSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
