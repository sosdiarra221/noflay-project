<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commercial_partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->enum('type', ['agence_immobiliere', 'notaire', 'banque', 'apporteur_affaires', 'autre'])->default('apporteur_affaires');
            $table->string('nom');
            $table->string('contact_nom')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->decimal('commission_pourcentage', 5, 2)->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->text('notes')->nullable();
            $table->string('motif_suppression')->nullable();
            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_partenaires');
    }
};
