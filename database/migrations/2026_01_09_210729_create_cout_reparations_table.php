<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couts_reparations', function (Blueprint $table) {
            $table->id();
            $table->string('type_element'); // sol, mur, plafond, menuiserie, etc.
            $table->string('nom'); // Peinture mur, Parquet, Moquette, etc.
            $table->string('description')->nullable();
            $table->string('unite'); // m², unité, ml, forfait
            $table->decimal('prix_unitaire', 10, 2);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couts_reparations');
    }
};