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
        Schema::table('modifications_historique', function (Blueprint $table) {
            $table->foreign(['modified_by'], 'modifications_historique_ibfk_1')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modifications_historique', function (Blueprint $table) {
            $table->dropForeign('modifications_historique_ibfk_1');
        });
    }
};
