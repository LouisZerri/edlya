<?php
// database/migrations/2026_01_13_000001_create_cles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etat_des_lieux_id');
            $table->string('type'); // Porte d'entrée, Boîte aux lettres, Cave, Garage, etc.
            $table->integer('nombre')->default(1);
            $table->string('commentaire')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('etat_des_lieux_id')
                ->references('id')
                ->on('etats_des_lieux')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cles');
    }
};