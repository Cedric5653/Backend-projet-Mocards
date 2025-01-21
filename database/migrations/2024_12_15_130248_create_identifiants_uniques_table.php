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
        Schema::create('identifiants_uniques', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('patient_id')->index('patient_id');
            $table->string('identifiant', 100)->unique('identifiant');
            $table->enum('methode', ['QR_CODE', 'NFC', 'BIOMETRIE']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identifiants_uniques');
    }
};
