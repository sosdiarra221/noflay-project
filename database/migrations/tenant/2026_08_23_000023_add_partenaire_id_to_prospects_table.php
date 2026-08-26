<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->foreignId('partenaire_id')->nullable()->after('source_id')->constrained('commercial_partenaires')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partenaire_id');
        });
    }
};
