<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piece_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('nom');
            $table->enum('etat', ['neuf', 'tres_bon', 'bon', 'usage', 'mauvais', 'hors_service'])->default('bon');
            $table->text('observations')->nullable();
            $table->json('degradations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};