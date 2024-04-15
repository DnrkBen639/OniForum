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
        Schema::table('notificacions', function (Blueprint $table) {
            $table->foreignId('idAmistad')->after('idUsuario')->constrained('amistads')->onDelete('cascade')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificacions', function (Blueprint $table) {
            $table->dropForeign(['idAmistad']);
            $table->dropColumn('idAmistad');
        });
    }
};
