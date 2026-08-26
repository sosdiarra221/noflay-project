<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facture_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->string('designation');
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_unitaire', 14, 2);
            $table->decimal('total', 14, 2);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture_lignes');
    }
};
