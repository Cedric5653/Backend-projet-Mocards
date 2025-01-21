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
        Schema::table('carnet_maternite', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'carnet_maternite_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carnet_maternite', function (Blueprint $table) {
            $table->dropForeign('carnet_maternite_ibfk_1');
        });
    }
};
