<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_absences', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('type_absence_id')->constrained('types_absence')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_retour');
            $table->decimal('nombre_jours', 6, 2);
            $table->text('motif')->nullable();
            $table->string('document')->nullable();
            $table->string('statut')->default('en_attente');
            $table->text('commentaire_statut')->nullable();
            $table->foreignId('cree_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_absences');
    }
};
