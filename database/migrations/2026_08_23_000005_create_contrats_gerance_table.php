<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_gerance', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('type_gerance')->default('gestion_locative'); // gestion_locative | gestion_vente | gestion_locative_vente
            $table->string('frais_gestion_mode')->default('pourcentage'); // pourcentage | montant_fixe
            $table->decimal('frais_gestion_valeur', 12, 2)->default(0);
            $table->string('tva_charge')->default('bailleur'); // bailleur | agence
            $table->string('taxe_charge')->default('bailleur');
            $table->string('tom_charge')->default('bailleur');
            $table->string('statut')->default('brouillon');
            $table->text('notes')->nullable();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index(['bailleur_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_gerance');
    }
};
