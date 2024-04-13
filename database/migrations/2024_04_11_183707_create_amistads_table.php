<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amistads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsuario1')->constrained('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('idUsuario2')->constrained('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('aceptado')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amistads');
    }
};
