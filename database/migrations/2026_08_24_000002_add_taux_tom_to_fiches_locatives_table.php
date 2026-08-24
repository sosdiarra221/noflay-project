<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiches_locatives', function (Blueprint $table) {
            $table->decimal('taux_tom', 5, 2)->default(0)->after('taxe_tom');
            $table->decimal('montant_tom', 12, 2)->default(0)->after('taux_tom');
        });
    }

    public function down(): void
    {
        Schema::table('fiches_locatives', function (Blueprint $table) {
            $table->dropColumn(['taux_tom', 'montant_tom']);
        });
    }
};
