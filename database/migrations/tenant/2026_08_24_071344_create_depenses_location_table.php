<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses_location', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->foreignId('contrat_location_id')->nullable()->constrained('contrats_location')->nullOnDelete();
            $table->foreignId('bailleur_id')->nullable()->constrained('bailleurs')->nullOnDelete();
            $table->foreignId('locataire_id')->nullable()->constrained('locataires')->nullOnDelete();
            $table->foreignId('categorie_depense_id')->constrained('categories_depense')->restrictOnDelete();
            $table->text('description');
            $table->decimal('montant_estime', 12, 2);
            $table->decimal('montant_final', 12, 2)->nullable();
            $table->string('fournisseur')->nullable();
            $table->boolean('urgence')->default(false);
            $table->enum('qui_supporte', ['bailleur', 'locataire', 'agence']);
            $table->foreignId('responsable_financier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('statut')->default('brouillon');
            // brouillon | en_attente_validation | approuvee | refusee | intervention_en_cours |
            // facture_recue | a_payer | payee | cloturee
            $table->text('motif_refus')->nullable();
            $table->date('date_validation')->nullable();
            $table->date('date_paiement')->nullable();
            $table->foreignId('mode_paiement_id')->nullable()->constrained('modes_paiement')->nullOnDelete();
            $table->text('commentaire')->nullable();
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses_location');
    }
};
