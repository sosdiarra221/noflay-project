<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->cascadeOnDelete();
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
        Schema::dropIfExists('devis_lignes');
    }
};
