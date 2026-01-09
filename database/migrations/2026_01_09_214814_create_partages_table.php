<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etat_des_lieux_id')->constrained('etats_des_lieux')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('email')->nullable();
            $table->enum('type', ['email', 'lien'])->default('lien');
            $table->timestamp('expire_at');
            $table->timestamp('consulte_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partages');
    }
};