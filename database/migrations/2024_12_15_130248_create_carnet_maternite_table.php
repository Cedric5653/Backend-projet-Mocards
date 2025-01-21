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
        Schema::create('carnet_maternite', function (Blueprint $table) {
            $table->integer('maternite_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->date('date_debut_grossesse')->nullable();
            $table->integer('nombre_grossesses')->nullable()->default(0);
            $table->integer('nombre_accouchements')->nullable()->default(0);
            $table->text('complications')->nullable();
            $table->string('groupage_sanguin', 5)->nullable();
            $table->enum('statut', ['vierge', 'en_cours', 'termine'])->nullable()->default('vierge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet_maternite');
    }
};
