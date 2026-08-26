<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('nom_pme')->nullable()->after('id');
            $table->string('statut')->default('actif')->after('nom_pme'); // actif | suspendu
            $table->string('plan')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['nom_pme', 'statut', 'plan']);
        });
    }
};
