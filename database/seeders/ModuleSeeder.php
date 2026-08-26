<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('modules') as $cle => $module) {
            Module::updateOrCreate(['cle' => $cle], $module + ['cle' => $cle, 'actif' => true]);
        }
    }
}
