<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: LOCATION_HABITATION, MANDAT_GERANCE...
            $table->string('name');
            $table->string('category')->nullable(); // LOCATION | MANDAT | ETAT_LIEUX | QUITTANCE | RELANCE...
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft | active | inactive | archived
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('supprime_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motif_suppression')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
