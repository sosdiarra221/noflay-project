<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('echeance_loyer_id')->constrained('echeances_loyer')->restrictOnDelete();
            $table->decimal('montant', 12, 2);
            $table->foreignId('mode_paiement_id')->nullable()->constrained('modes_paiement')->nullOnDelete();
            $table->date('date_paiement');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('enregistre_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
