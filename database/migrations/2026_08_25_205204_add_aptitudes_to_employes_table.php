<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->boolean('permis_conduire')->default(false)->after('piece_identite_numero');
            $table->boolean('arts_martiaux')->default(false)->after('permis_conduire');
            $table->boolean('service_militaire')->default(false)->after('arts_martiaux');
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn(['permis_conduire', 'arts_martiaux', 'service_militaire']);
        });
    }
};
