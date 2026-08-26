<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_sites', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('nom')->constrained('facturation_clients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rh_sites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
