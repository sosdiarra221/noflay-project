<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE paiements MODIFY echeance_loyer_id BIGINT UNSIGNED NULL');

        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('contrat_location_id')->nullable()->after('echeance_loyer_id')->constrained('contrats_location')->nullOnDelete();
            $table->enum('type', ['loyer', 'entree'])->default('loyer')->after('contrat_location_id');
            $table->decimal('part_bailleur', 12, 2)->nullable()->after('montant');
            $table->decimal('part_commission_agence', 12, 2)->nullable()->after('part_bailleur');
            $table->decimal('part_caution', 12, 2)->nullable()->after('part_commission_agence');
            $table->decimal('part_frais_agence', 12, 2)->nullable()->after('part_caution');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['contrat_location_id']);
            $table->dropColumn(['contrat_location_id', 'type', 'part_bailleur', 'part_commission_agence', 'part_caution', 'part_frais_agence']);
        });

        DB::statement('ALTER TABLE paiements MODIFY echeance_loyer_id BIGINT UNSIGNED NOT NULL');
    }
};
