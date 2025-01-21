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
        Schema::create('carte_urgence', function (Blueprint $table) {
            $table->integer('carte_id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->string('groupe_sanguin', 3)->nullable();
            $table->text('allergies')->nullable();
            $table->text('maladies_chroniques')->nullable();
            $table->string('numero_proches', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('electrophorese', 50)->nullable();
            $table->text('maladies_hereditaires')->nullable();
            $table->text('handicap')->nullable();
            $table->text('acute_visuelle')->nullable();
            $table->boolean('dialyse')->nullable()->default(false);
            $table->text('constantes_stables')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carte_urgence');
    }
};
