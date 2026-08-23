<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances_loyer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_location_id')->constrained('contrats_location')->restrictOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->unsignedTinyInteger('mois');
            $table->date('date_echeance');
            $table->decimal('montant_attendu', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->string('statut')->default('a_venir'); // a_venir | echu | partiellement_paye | paye | en_retard | annule
            $table->timestamps();

            $table->unique(['contrat_location_id', 'annee', 'mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances_loyer');
    }
};
