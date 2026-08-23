<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bailleurs', function (Blueprint $table) {
            $table->string('rc')->nullable()->after('ninea');
        });

        Schema::table('locataires', function (Blueprint $table) {
            $table->string('ninea')->nullable()->after('type_locataire');
            $table->string('rc')->nullable()->after('ninea');
        });
    }

    public function down(): void
    {
        Schema::table('bailleurs', function (Blueprint $table) {
            $table->dropColumn('rc');
        });

        Schema::table('locataires', function (Blueprint $table) {
            $table->dropColumn(['ninea', 'rc']);
        });
    }
};
