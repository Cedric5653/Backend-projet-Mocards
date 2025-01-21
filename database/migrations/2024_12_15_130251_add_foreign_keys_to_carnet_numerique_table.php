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
        Schema::table('carnet_numerique', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'carnet_numerique_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['medecin_id'], 'carnet_numerique_ibfk_2')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['last_modified_by'], 'fk_carnet_modified_user')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['consultation_id'], 'fk_consultation_carnet')->references(['consultation_id'])->on('consultations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['last_modified_by'], 'fk_last_modified_user')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carnet_numerique', function (Blueprint $table) {
            $table->dropForeign('carnet_numerique_ibfk_1');
            $table->dropForeign('carnet_numerique_ibfk_2');
            $table->dropForeign('fk_carnet_modified_user');
            $table->dropForeign('fk_consultation_carnet');
            $table->dropForeign('fk_last_modified_user');
        });
    }
};
