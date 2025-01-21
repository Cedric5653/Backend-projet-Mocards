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
        Schema::create('consultations', function (Blueprint $table) {
            $table->integer('consultation_id', true);
            $table->integer('patient_id');
            $table->date('date_consultation');
            $table->enum('type_consultation', ['routine', 'urgence', 'suivi', 'specialiste']);
            $table->string('centre_sante');
            $table->text('symptomes')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('prescriptions')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('hospitalisation')->nullable()->default(false);
            $table->integer('duree_hospitalisation')->nullable();
            $table->integer('medecin_id')->nullable()->index('medecin_id');

            $table->index(['patient_id', 'date_consultation', 'type_consultation'], 'idx_consultation_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
