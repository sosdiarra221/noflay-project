<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->text('motif_resiliation')->nullable()->after('motif_suppression');
            $table->foreignId('renouvele_depuis_id')->nullable()->after('motif_resiliation')->constrained('contrats_location')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->dropConstrainedForeignId('renouvele_depuis_id');
            $table->dropColumn('motif_resiliation');
        });
    }
};
