<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Fonction" (texte libre) devient "Poste" (référentiel dédié) — on migre d'abord les valeurs
     * existantes en postes avant de retirer la colonne, pour ne perdre aucune donnée déjà saisie.
     */
    public function up(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->foreignId('poste_id')->nullable()->after('fonction')->constrained('postes')->restrictOnDelete();
        });

        $fonctions = DB::table('employes')->whereNotNull('fonction')->where('fonction', '!=', '')->pluck('fonction')->unique();
        foreach ($fonctions as $fonction) {
            $posteId = DB::table('postes')->where('nom', $fonction)->value('id');
            if (! $posteId) {
                $posteId = DB::table('postes')->insertGetId([
                    'nom' => $fonction,
                    'actif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('employes')->where('fonction', $fonction)->update(['poste_id' => $posteId]);
        }

        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn('fonction');
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->string('fonction')->nullable()->after('categorie_fonction');
        });

        DB::table('employes')->update([
            'fonction' => DB::raw('(select nom from postes where postes.id = employes.poste_id)'),
        ]);

        Schema::table('employes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('poste_id');
        });
    }
};
