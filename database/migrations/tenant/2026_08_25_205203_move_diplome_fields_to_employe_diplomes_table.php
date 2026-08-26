<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Niveau d'étude"/"Intitulé du diplôme" (2 champs scalaires) deviennent une liste de diplômes
     * ajoutables dynamiquement — on migre d'abord les valeurs existantes avant de retirer les colonnes.
     */
    public function up(): void
    {
        $employes = DB::table('employes')
            ->whereNotNull('intitule_diplome')
            ->where('intitule_diplome', '!=', '')
            ->get(['id', 'niveau_etude', 'intitule_diplome']);

        foreach ($employes as $employe) {
            DB::table('employe_diplomes')->insert([
                'employe_id' => $employe->id,
                'intitule' => $employe->intitule_diplome,
                'niveau' => $employe->niveau_etude,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn(['niveau_etude', 'intitule_diplome']);
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->string('niveau_etude')->nullable()->after('categorie_fonction');
            $table->string('intitule_diplome')->nullable()->after('niveau_etude');
        });
    }
};
