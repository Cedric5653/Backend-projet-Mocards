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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->integer('rdv_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->integer('medecin_id')->index('medecin_id');
            $table->dateTime('date_rdv');
            $table->text('motif')->nullable();
            $table->enum('statut', ['programmé', 'confirmé', 'annulé', 'terminé']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
