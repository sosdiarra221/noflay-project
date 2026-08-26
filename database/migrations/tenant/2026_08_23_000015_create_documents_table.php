<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('type_mime')->nullable();
            $table->unsignedBigInteger('taille')->nullable();
            $table->string('categorie')->nullable();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
