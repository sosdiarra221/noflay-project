<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identifiant')->nullable()->unique()->after('email');
            $table->string('code_pin')->nullable()->after('password');
            $table->string('photo')->nullable()->after('code_pin');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('photo');
            $table->timestamp('derniere_activite_at')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['identifiant', 'code_pin', 'photo', 'statut', 'derniere_activite_at']);
        });
    }
};
