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
        Schema::table('logs_acces', function (Blueprint $table) {
            $table->foreign(['user_id'], 'logs_acces_ibfk_1')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['patient_id'], 'logs_acces_ibfk_2')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs_acces', function (Blueprint $table) {
            $table->dropForeign('logs_acces_ibfk_1');
            $table->dropForeign('logs_acces_ibfk_2');
        });
    }
};
