<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reversements_bailleurs', function (Blueprint $table) {
            $table->decimal('montant_depenses', 12, 2)->default(0)->after('montant_frais_gestion');
        });
    }

    public function down(): void
    {
        Schema::table('reversements_bailleurs', function (Blueprint $table) {
            $table->dropColumn('montant_depenses');
        });
    }
};
