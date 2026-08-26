<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('ancien_departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->foreignId('nouveau_departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->string('anciens_sites')->nullable(); // libellés concaténés, pour trace lisible même si les sites changent après coup
            $table->string('nouveaux_sites')->nullable();
            $table->date('date_affectation');
            $table->text('motif')->nullable();
            $table->foreignId('effectue_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_affectations');
    }
};
