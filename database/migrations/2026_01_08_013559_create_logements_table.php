<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('adresse');
            $table->string('code_postal', 10);
            $table->string('ville');
            $table->enum('type', ['appartement', 'maison', 'studio', 'local_commercial'])->default('appartement');
            $table->decimal('surface', 8, 2)->nullable();
            $table->integer('nb_pieces')->nullable();
            $table->text('description')->nullable();
            $table->string('photo_principale')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logements');
    }
};