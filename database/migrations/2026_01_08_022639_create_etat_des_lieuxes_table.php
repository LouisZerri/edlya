<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etats_des_lieux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie']);
            $table->date('date_realisation');
            $table->string('locataire_nom');
            $table->string('locataire_email')->nullable();
            $table->string('locataire_telephone')->nullable();
            $table->text('observations_generales')->nullable();
            $table->enum('statut', ['brouillon', 'en_cours', 'termine', 'signe'])->default('brouillon');
            $table->timestamp('date_signature')->nullable();
            $table->text('signature_bailleur')->nullable();
            $table->text('signature_locataire')->nullable();
            $table->timestamp('date_signature_bailleur')->nullable();
            $table->timestamp('date_signature_locataire')->nullable();
            $table->string('code_validation', 6)->nullable();
            $table->timestamp('code_validation_expire_at')->nullable();
            $table->timestamp('code_validation_verifie_at')->nullable();
            $table->string('signature_ip')->nullable();
            $table->text('signature_user_agent')->nullable();
            $table->string('signature_token', 64)->nullable()->unique();
            $table->timestamp('signature_token_expire_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etats_des_lieux');
    }
};