<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglages', function (Blueprint $table) {
            $table->id();

            // Informations de la société
            $table->string('nom_societe')->nullable();
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('site_web')->nullable();

            // Devise utilisée par l'application
            $table->foreignId('devise_id')->nullable()->constrained('devises')->nullOnDelete();

            // Configuration SMTP
            $table->string('smtp_type')->default('smtp'); // smtp, sendmail, log, mailgun, ses, postmark...
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable(); // tls, ssl, none
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglages');
    }
};
