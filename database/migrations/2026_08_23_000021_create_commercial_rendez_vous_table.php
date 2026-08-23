<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commercial_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->nullable()->constrained('prospects')->nullOnDelete();
            $table->string('titre');
            $table->enum('type', ['rendez_vous', 'visite', 'appel', 'autre'])->default('rendez_vous');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->string('lieu')->nullable();
            $table->text('description')->nullable();
            $table->enum('statut', ['planifie', 'termine', 'annule'])->default('planifie');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_rendez_vous');
    }
};
