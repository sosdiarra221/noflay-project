<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reglages', function (Blueprint $table) {
            $table->string('ninea')->nullable()->after('nom_societe');
        });
    }

    public function down(): void
    {
        Schema::table('reglages', function (Blueprint $table) {
            $table->dropColumn('ninea');
        });
    }
};
