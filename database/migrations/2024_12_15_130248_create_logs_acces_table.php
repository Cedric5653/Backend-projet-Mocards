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
        Schema::create('logs_acces', function (Blueprint $table) {
            $table->integer('log_id', true);
            $table->integer('user_id')->index('user_id');
            $table->integer('patient_id')->index('patient_id');
            $table->enum('type_acces', ['lecture', 'modification', 'suppression']);
            $table->string('table_concernee', 50);
            $table->timestamp('date_acces')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_acces');
    }
};
