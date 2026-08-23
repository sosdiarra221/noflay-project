<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiches_locatives', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reference')->unique();
            $table->foreignId('contrat_location_id')->constrained('contrats_location')->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->unsignedTinyInteger('mois');
            $table->decimal('loyer_mensuel', 12, 2);
            $table->decimal('arrieres', 12, 2)->default(0);
            $table->decimal('frais_agence', 12, 2)->default(0);
            $table->decimal('taxe_tom', 12, 2)->default(0);
            $table->decimal('taux_tva', 5, 2)->default(0);
            $table->decimal('montant_tva', 12, 2)->default(0);
            $table->decimal('montant_total', 12, 2);
            $table->date('date_limite_paiement')->nullable();
            $table->foreignId('genere_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['contrat_location_id', 'annee', 'mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiches_locatives');
    }
};
