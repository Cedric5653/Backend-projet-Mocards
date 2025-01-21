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
        Schema::table('carte_urgence', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'carte_urgence_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carte_urgence', function (Blueprint $table) {
            $table->dropForeign('carte_urgence_ibfk_1');
        });
    }
};
