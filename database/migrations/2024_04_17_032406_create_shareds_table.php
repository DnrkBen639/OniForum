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
        Schema::create('shareds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPublicacion');
            $table->unsignedBigInteger('idContent');
            $table->timestamps();

            $table->foreign('idPublicacion')->references('id')->on('publicacions')->onDelete('cascade');
            $table->foreign('idContent')->references('id')->on('contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shareds');
    }
};
