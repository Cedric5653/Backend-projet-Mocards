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
        Schema::create('examens_laboratoire', function (Blueprint $table) {
            $table->integer('examen_id', true);
            $table->integer('patient_id');
            $table->enum('type_examen', ['LMB', 'radio', 'echo', 'autre']);
            $table->date('date_examen');
            $table->text('resultat')->nullable();
            $table->string('centre_examen')->nullable();
            $table->integer('medecin_id')->nullable()->index('medecin_id');
            $table->integer('document_id')->nullable()->index('fk_examen_document');

            $table->index(['patient_id', 'type_examen', 'date_examen'], 'idx_examen_complet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examens_laboratoire');
    }
};
