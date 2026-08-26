<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_epouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('nom_complet');
            $table->string('telephone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_epouses');
    }
};
