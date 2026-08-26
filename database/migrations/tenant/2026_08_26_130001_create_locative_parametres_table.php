<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Informations d'agence propres au module Locative (nom, logo, coordonnées) — utilisées sur les
// documents générés dans ce module, séparées des réglages généraux (table `reglages`) utilisés
// par Facturation/RH/Gestion Document.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locative_parametres', function (Blueprint $table) {
            $table->id();
            $table->string('nom_societe')->nullable();
            $table->string('logo')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locative_parametres');
    }
};
