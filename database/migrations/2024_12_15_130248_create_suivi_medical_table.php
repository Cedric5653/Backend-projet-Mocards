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
        Schema::create('suivi_medical', function (Blueprint $table) {
            $table->integer('suivi_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->integer('medecin_id')->index('medecin_id');
            $table->text('diagnostic')->nullable();
            $table->text('prescription')->nullable();
            $table->text('posologie')->nullable();
            $table->text('observations')->nullable();
            $table->date('date_consultation')->index('idx_suivi_date');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_medical');
    }
};
