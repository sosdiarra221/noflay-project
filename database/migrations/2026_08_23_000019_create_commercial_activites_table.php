<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commercial_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->restrictOnDelete();
            $table->string('type'); // appel | email | whatsapp | sms | visite | rendez_vous | note | relance | document | autre
            $table->string('objet');
            $table->text('description')->nullable();
            $table->dateTime('date_activite');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_activites');
    }
};
