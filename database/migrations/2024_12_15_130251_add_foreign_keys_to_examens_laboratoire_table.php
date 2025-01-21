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
        Schema::table('examens_laboratoire', function (Blueprint $table) {
            $table->foreign(['patient_id'], 'examens_laboratoire_ibfk_1')->references(['patient_id'])->on('patients')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['medecin_id'], 'examens_laboratoire_ibfk_2')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['document_id'], 'fk_examen_document')->references(['document_id'])->on('documents_medicaux')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examens_laboratoire', function (Blueprint $table) {
            $table->dropForeign('examens_laboratoire_ibfk_1');
            $table->dropForeign('examens_laboratoire_ibfk_2');
            $table->dropForeign('fk_examen_document');
        });
    }
};
