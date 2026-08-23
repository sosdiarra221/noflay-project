<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_location', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('loyer_mensuel', 12, 2);
            $table->decimal('depot_garantie', 12, 2)->default(0);
            $table->unsignedTinyInteger('jour_echeance')->default(1);
            $table->decimal('charges', 12, 2)->default(0);
            $table->string('periodicite')->default('mensuelle');
            $table->foreignId('mode_paiement_prefere_id')->nullable()->constrained('modes_paiement')->nullOnDelete();
            $table->string('statut')->default('actif');
            $table->text('notes')->nullable();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('location_id');
            $table->index('bien_id');
            $table->index('bailleur_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_location');
    }
};
