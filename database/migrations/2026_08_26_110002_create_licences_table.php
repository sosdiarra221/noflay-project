<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('genere_par_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index(['tenant_id', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};
