<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents_generes')->cascadeOnDelete();
            $table->string('action'); // creation | edition | regeneration | changement_statut
            $table->longText('content_before')->nullable();
            $table->longText('content_after')->nullable();
            $table->json('changes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
    }
};
