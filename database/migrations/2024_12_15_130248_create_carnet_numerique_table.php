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
        Schema::create('carnet_numerique', function (Blueprint $table) {
            $table->integer('carnet_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->enum('type_enregistrement', ['consultation', 'traitement', 'bilan', 'prescription']);
            $table->text('description')->nullable();
            $table->date('date_enregistrement')->index('idx_carnet_date');
            $table->integer('medecin_id')->nullable()->index('medecin_id');
            $table->string('hôpital')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('consultation_id')->nullable()->index('fk_consultation_carnet');
            $table->integer('last_modified_by')->nullable()->index('fk_carnet_modified_user');
            $table->timestamp('last_modified_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet_numerique');
    }
};
