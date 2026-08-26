<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employe_documents', function (Blueprint $table) {
            $table->string('type_mime')->nullable()->after('chemin_fichier');
            $table->unsignedBigInteger('taille')->nullable()->after('type_mime');
        });
    }

    public function down(): void
    {
        Schema::table('employe_documents', function (Blueprint $table) {
            $table->dropColumn(['type_mime', 'taille']);
        });
    }
};
