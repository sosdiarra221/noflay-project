<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('status')->default('draft'); // draft | active | inactive | archived
            $table->longText('content'); // HTML — la source de vérité, jamais du DOCX
            $table->text('notes')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(['document_template_id', 'version']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_template_versions');
    }
};
