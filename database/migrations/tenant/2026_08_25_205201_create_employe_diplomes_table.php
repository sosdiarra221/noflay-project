<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_diplomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('intitule');
            $table->string('niveau')->nullable();
            $table->string('annee_obtention', 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_diplomes');
    }
};
