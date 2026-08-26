<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des documents GÉNÉRÉS par le nouveau moteur de modèles (App\Models\Documents\Document).
     * Nommée "documents_generes" (et non "documents") pour ne pas entrer en conflit avec la table
     * "documents" déjà utilisée par le système de pièces jointes polymorphe existant (App\Models\Document).
     */
    public function up(): void
    {
        Schema::create('documents_generes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // DOC-{annee}-{sequence}, via NumeroService
            $table->string('type'); // code App\Services\Documents\DocumentType (LOCATION_HABITATION, MANDAT_GERANCE...)
            $table->string('title');

            $table->foreignId('document_template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->foreignId('document_template_version_id')->nullable()->constrained('document_template_versions')->nullOnDelete();

            // Rattachement polymorphe (ContratLocation, ContratGerance...).
            $table->string('documentable_type')->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();

            $table->longText('content'); // HTML — copie indépendante du modèle dès la génération
            $table->string('status')->default('generated'); // draft|generated|review|validated|signed|archived|cancelled

            // Placeholders de signature électronique (aucun flux de signature réel — hors périmètre, cf. phase 6).
            $table->string('signature_status')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->json('context_snapshot')->nullable(); // variables résolues au moment de la génération

            $table->foreignId('generated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_generes');
    }
};
