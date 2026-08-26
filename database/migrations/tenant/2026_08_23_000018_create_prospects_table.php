<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->foreignId('type_demande_id')->nullable()->constrained('commercial_types_demande')->nullOnDelete();
            $table->text('besoin')->nullable();
            $table->decimal('budget_min', 14, 2)->nullable();
            $table->decimal('budget_max', 14, 2)->nullable();
            $table->string('devise')->default('FCFA');
            $table->foreignId('source_id')->nullable()->constrained('commercial_sources')->nullOnDelete();
            $table->string('statut')->default('non_traite'); // non_traite | en_cours | gagne | perdu | annule
            $table->foreignId('commercial_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('converti_en')->nullable(); // location | vente | achat | gerance
            $table->timestamp('converti_le')->nullable();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
