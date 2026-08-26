<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->string('titre');
            $table->foreignId('categorie_bien_id')->nullable()->constrained('categories_biens')->nullOnDelete();
            $table->string('adresse')->nullable();
            $table->string('zone')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->restrictOnDelete();
            $table->foreignId('gerance_id')->nullable()->constrained('contrats_gerance')->nullOnDelete();
            $table->string('type_exploitation')->default('location'); // location | vente
            $table->decimal('loyer_mensuel', 12, 2)->nullable();
            $table->decimal('prix_vente', 14, 2)->nullable();
            $table->string('frais_gestion_mode')->nullable();
            $table->decimal('frais_gestion_valeur', 12, 2)->nullable();
            $table->string('statut')->default('disponible');
            $table->text('notes')->nullable();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('bailleur_id');
            $table->index('gerance_id');
            $table->index('statut');
            $table->index('type_exploitation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
