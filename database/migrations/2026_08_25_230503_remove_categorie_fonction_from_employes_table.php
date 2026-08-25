<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Fonction" (agent terrain/superviseur/staff) est retirée — "Poste" couvre désormais
     * seul le rattachement métier de l'employé, sans avoir besoin de cette double classification.
     */
    public function up(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn('categorie_fonction');
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->string('categorie_fonction')->nullable()->after('poste_id');
        });
    }
};
