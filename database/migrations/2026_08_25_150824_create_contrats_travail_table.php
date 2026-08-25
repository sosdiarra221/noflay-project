<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_travail', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type_contrat'); // cdi | cdd | essai | stage
            $table->date('date_debut');
            $table->date('date_prevu_fin')->nullable(); // date de fin prévue (jamais écrasée lors d'un renouvellement)
            $table->date('date_fin')->nullable(); // date de fin réelle (résiliation anticipée, fin effective...)
            $table->text('motif')->nullable();
            $table->decimal('montant', 12, 2)->nullable(); // salaire contractuel
            $table->string('document')->nullable(); // contrat numérisé joint
            $table->string('etat')->default('actif'); // actif | cloture
            $table->foreignId('contrat_precedent_id')->nullable()->constrained('contrats_travail')->nullOnDelete();
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('etat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_travail');
    }
};
