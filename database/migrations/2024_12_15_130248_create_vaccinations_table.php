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
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->integer('vaccination_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->string('type_vaccin', 100);
            $table->date('date_vaccination');
            $table->date('rappel_prevu')->nullable();
            $table->string('centre_vaccination')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
