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
        Schema::create('localisation', function (Blueprint $table) {
            $table->integer('localisation_id', true);
            $table->string('region', 100);
            $table->string('province', 100);
            $table->string('ville', 100);
            $table->string('district_sanitaire', 100);

            $table->index(['region', 'province', 'ville', 'district_sanitaire'], 'idx_localisation_hierarchie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localisation');
    }
};
