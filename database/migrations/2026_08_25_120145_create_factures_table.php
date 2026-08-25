<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('date_facture');
            $table->foreignId('client_id')->constrained('facturation_clients')->cascadeOnDelete();
            $table->foreignId('devis_id')->nullable()->constrained('devis')->nullOnDelete();
            $table->string('source')->nullable(); // ex: "Devis DEV-001/26 du 25/08/2026"
            $table->string('statut')->default('emise'); // emise | payee | annulee
            $table->boolean('appliquer_tva')->default(false);
            $table->decimal('taux_tva', 5, 2)->default(0);
            $table->decimal('sous_total_ht', 14, 2)->default(0);
            $table->decimal('montant_tva', 14, 2)->default(0);
            $table->decimal('total_ttc', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
