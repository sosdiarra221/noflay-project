<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Table sessions centralisée : évite qu'une session (tenant OU admin central) ne soit écrite au
// mauvais endroit lorsque stancl/tenancy bascule temporairement la connexion par défaut pendant
// une opération de provisioning (ex: création d'une nouvelle PME). Voir SESSION_CONNECTION=central.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
