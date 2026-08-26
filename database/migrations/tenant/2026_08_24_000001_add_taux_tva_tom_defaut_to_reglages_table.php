<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reglages', function (Blueprint $table) {
            $table->decimal('taux_tva_defaut', 5, 2)->default(18)->after('ninea');
            $table->decimal('taux_tom_defaut', 5, 2)->default(0)->after('taux_tva_defaut');
        });
    }

    public function down(): void
    {
        Schema::table('reglages', function (Blueprint $table) {
            $table->dropColumn(['taux_tva_defaut', 'taux_tom_defaut']);
        });
    }
};
