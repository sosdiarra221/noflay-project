<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commercial_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut');
            $table->string('commentaire')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_status_histories');
    }
};
