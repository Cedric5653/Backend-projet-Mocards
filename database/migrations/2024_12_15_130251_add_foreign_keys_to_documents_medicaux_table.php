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
        Schema::table('documents_medicaux', function (Blueprint $table) {
            $table->foreign(['carnet_id'], 'documents_medicaux_ibfk_1')->references(['carnet_id'])->on('carnet_numerique')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['created_by'], 'documents_medicaux_ibfk_2')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents_medicaux', function (Blueprint $table) {
            $table->dropForeign('documents_medicaux_ibfk_1');
            $table->dropForeign('documents_medicaux_ibfk_2');
        });
    }
};
