<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cautions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_location_id')->unique()->constrained('contrats_location')->cascadeOnDelete();
            $table->decimal('montant_total', 12, 2);
            $table->decimal('part_bailleur', 12, 2);
            $table->decimal('part_agence', 12, 2);
            $table->enum('statut', ['detenue', 'partiellement_restituee', 'restituee'])->default('detenue');
            $table->decimal('montant_retenu', 12, 2)->default(0);
            $table->text('motif_retenue')->nullable();
            $table->decimal('montant_restitue', 12, 2)->default(0);
            $table->date('date_restitution')->nullable();
            $table->foreignId('restituee_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cautions');
    }
};
