<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('photo')->nullable();

            // État civil
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe')->nullable(); // homme | femme
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('situation_matrimoniale')->nullable(); // celibataire | marie | divorce | veuf
            $table->string('piece_identite_type')->nullable();
            $table->string('piece_identite_numero')->nullable();

            // Contact
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();

            // Poste / rattachement
            $table->string('fonction'); // intitulé du poste
            $table->string('categorie_fonction'); // agent_terrain | superviseur | staff
            $table->foreignId('departement_id')->constrained('departements')->restrictOnDelete();

            // Diplômes / langues
            $table->string('niveau_etude')->nullable();
            $table->string('intitule_diplome')->nullable();
            $table->string('langues_parlees')->nullable();
            $table->string('langues_lues')->nullable();

            // Coordonnées bancaires
            $table->string('banque')->nullable();
            $table->string('compte_bancaire')->nullable();

            // Contact d'urgence
            $table->string('personne_urgence_nom')->nullable();
            $table->string('personne_urgence_telephone')->nullable();
            $table->string('personne_urgence_lien')->nullable();

            // Congés & emploi
            $table->decimal('solde_conges', 6, 2)->default(0);
            $table->date('date_embauche');
            $table->string('statut')->default('actif'); // actif | sortie
            $table->date('date_sortie')->nullable();
            $table->string('motif_sortie')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
