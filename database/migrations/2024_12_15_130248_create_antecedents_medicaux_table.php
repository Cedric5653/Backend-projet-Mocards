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
        Schema::create('antecedents_medicaux', function (Blueprint $table) {
            $table->integer('antecedent_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->enum('type_antecedent', ['medical', 'chirurgical', 'familial']);
            $table->text('description')->nullable();
            $table->date('date_evenement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedents_medicaux');
    }
};
