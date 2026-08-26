<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bailleurs', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->string('type')->default('particulier'); // particulier | entreprise
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('piece_identite_type')->nullable();
            $table->string('piece_identite_numero')->nullable();
            $table->string('ninea')->nullable();
            $table->text('coordonnees_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->string('statut')->default('actif');

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bailleurs');
    }
};
