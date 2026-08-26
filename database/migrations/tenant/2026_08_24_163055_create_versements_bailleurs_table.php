<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal des versements réellement effectués à un bailleur (paiements normaux ou avances),
     * indépendant de toute période : le solde restant dû à un bailleur est toujours recalculé
     * dynamiquement (loyers encaissés - commission - dépenses - somme des versements), ce qui
     * fait automatiquement remonter comme « arriéré » tout montant non versé d'un mois antérieur,
     * sans logique de report à coder séparément.
     */
    public function up(): void
    {
        Schema::create('versements_bailleurs', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->restrictOnDelete();
            $table->decimal('montant', 12, 2);
            $table->enum('type', ['normal', 'avance'])->default('normal');
            $table->date('date_versement');
            $table->foreignId('mode_paiement_id')->nullable()->constrained('modes_paiement')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('effectue_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versements_bailleurs');
    }
};
