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
        Schema::table('identifiants_uniques', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'identifiants_uniques_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('identifiants_uniques', function (Blueprint $table) {
            $table->dropForeign('identifiants_uniques_ibfk_1');
        });
    }
};
