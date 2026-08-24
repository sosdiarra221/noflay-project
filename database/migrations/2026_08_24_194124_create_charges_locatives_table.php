<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Charges locatives (électricité, eau, wifi...) : à la charge du locataire, distinctes du
     * loyer dû au titre du contrat — ajoutées manuellement depuis la fiche d'une location, elles
     * n'entrent jamais dans le calcul des échéances de loyer.
     */
    public function up(): void
    {
        Schema::create('charges_locatives', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('contrat_location_id')->constrained('contrats_location')->cascadeOnDelete();
            $table->string('type_charge');
            $table->string('titre');
            $table->decimal('montant', 12, 2);
            $table->text('description')->nullable();
            $table->boolean('reglee_par_locataire')->default(true);
            $table->unsignedTinyInteger('frequence_mois')->default(1);
            $table->enum('statut', ['a_payer', 'payee'])->default('a_payer');
            $table->date('date_charge');
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges_locatives');
    }
};
