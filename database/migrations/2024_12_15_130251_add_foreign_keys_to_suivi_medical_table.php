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
        Schema::table('suivi_medical', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'suivi_medical_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['medecin_id'], 'suivi_medical_ibfk_2')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suivi_medical', function (Blueprint $table) {
            $table->dropForeign('suivi_medical_ibfk_1');
            $table->dropForeign('suivi_medical_ibfk_2');
        });
    }
};
