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
        Schema::create('documents_medicaux', function (Blueprint $table) {
            $table->integer('document_id', true);
            $table->integer('carnet_id')->index('carnet_id');
            $table->enum('type_document', ['radiographie', 'analyse_sang', 'ordonnance', 'compte_rendu']);
            $table->string('fichier_url');
            $table->date('date_creation');
            $table->integer('created_by')->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_medicaux');
    }
};
