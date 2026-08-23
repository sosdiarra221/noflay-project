<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('echeances_loyer', function (Blueprint $table) {
            $table->string('numero_quittance')->nullable()->unique()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('echeances_loyer', function (Blueprint $table) {
            $table->dropColumn('numero_quittance');
        });
    }
};
