<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journaux_activite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('module')->default('locative');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->string('motif')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journaux_activite');
    }
};
