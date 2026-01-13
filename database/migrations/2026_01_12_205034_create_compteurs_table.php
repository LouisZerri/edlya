<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compteurs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etat_des_lieux_id');
            $table->enum('type', ['electricite', 'eau_froide', 'eau_chaude', 'gaz']);
            $table->string('numero')->nullable();
            $table->string('index')->nullable();
            $table->string('photo')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->foreign('etat_des_lieux_id')
                ->references('id')
                ->on('etats_des_lieux')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compteurs');
    }
};