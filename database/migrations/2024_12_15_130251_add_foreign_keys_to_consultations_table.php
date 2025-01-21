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
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'consultations_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['medecin_id'], 'consultations_ibfk_2')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign('consultations_ibfk_1');
            $table->dropForeign('consultations_ibfk_2');
        });
    }
};
