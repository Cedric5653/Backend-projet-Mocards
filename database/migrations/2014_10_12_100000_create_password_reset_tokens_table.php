<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id(); // Ajouter un ID auto-incrémenté
            $table->string('email')->index(); // Colonne email
            $table->string('token'); // Colonne token
            $table->timestamps(); // Timestamps created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
