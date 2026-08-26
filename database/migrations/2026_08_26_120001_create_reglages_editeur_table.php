<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Réglages de l'éditeur du logiciel (identité visuelle, SMTP, intégrations) — une seule ligne,
// distincte des réglages de chaque société (voir la table `reglages`, par tenant).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglages_editeur', function (Blueprint $table) {
            $table->id();

            $table->string('nom_application')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Configuration SMTP du logiciel (emails système, hors emails d'une société)
            $table->string('smtp_type')->default('log');
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            // Intégrations
            $table->string('pusher_app_id')->nullable();
            $table->string('pusher_key')->nullable();
            $table->string('pusher_secret')->nullable();
            $table->string('pusher_cluster')->nullable();
            $table->string('firebase_api_key')->nullable();
            $table->string('firebase_project_id')->nullable();
            $table->string('firebase_messaging_sender_id')->nullable();
            $table->string('firebase_app_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglages_editeur');
    }
};
