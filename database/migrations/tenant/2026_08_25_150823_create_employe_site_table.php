<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_site', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('rh_sites')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['employe_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_site');
    }
};
