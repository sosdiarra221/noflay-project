<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->enum('type_location', ['habitation', 'commercial'])->nullable()->after('bien_id');
            $table->boolean('appliquer_tva')->default(false)->after('charges');
            $table->boolean('appliquer_tom')->default(false)->after('appliquer_tva');
            $table->decimal('depot_garantie_part_bailleur', 12, 2)->nullable()->after('depot_garantie');
            $table->decimal('depot_garantie_part_agence', 12, 2)->nullable()->after('depot_garantie_part_bailleur');
        });
    }

    public function down(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->dropColumn(['type_location', 'appliquer_tva', 'appliquer_tom', 'depot_garantie_part_bailleur', 'depot_garantie_part_agence']);
        });
    }
};
