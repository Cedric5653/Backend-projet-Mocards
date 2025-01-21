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
        Schema::create('modifications_historique', function (Blueprint $table) {
            $table->integer('historique_id', true);
            $table->string('table_name', 50);
            $table->integer('record_id');
            $table->string('field_modified', 50);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->integer('modified_by')->index('modified_by');
            $table->timestamp('modified_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modifications_historique');
    }
};
