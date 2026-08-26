<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE devis ALTER COLUMN statut SET DEFAULT 'nouveau'");
        DB::statement("UPDATE devis SET statut = 'nouveau' WHERE statut = 'brouillon'");
        DB::statement("UPDATE devis SET statut = 'perdu' WHERE statut = 'refuse'");
        DB::statement("UPDATE devis SET statut = 'annule' WHERE statut = 'expire'");
        DB::statement("UPDATE devis SET statut = 'en_negociation' WHERE statut = 'envoye'");
        DB::statement("UPDATE devis SET statut = 'gagne' WHERE statut = 'accepte'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE devis ALTER COLUMN statut SET DEFAULT 'brouillon'");
    }
};
