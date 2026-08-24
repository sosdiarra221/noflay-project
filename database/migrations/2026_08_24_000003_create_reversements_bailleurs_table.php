<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reversements_bailleurs', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->restrictOnDelete();
            $table->unsignedSmallInteger('periode_annee');
            $table->unsignedTinyInteger('periode_mois');
            $table->decimal('montant_encaisse', 12, 2);
            $table->decimal('montant_frais_gestion', 12, 2);
            $table->decimal('montant_net', 12, 2);
            $table->enum('statut', ['a_verser', 'verse'])->default('a_verser');
            $table->date('date_versement')->nullable();
            $table->foreignId('mode_paiement_id')->nullable()->constrained('modes_paiement')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('effectue_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['bailleur_id', 'periode_annee', 'periode_mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reversements_bailleurs');
    }
};
