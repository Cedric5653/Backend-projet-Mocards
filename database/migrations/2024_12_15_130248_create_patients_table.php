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
        Schema::create('patients', function (Blueprint $table) {
            $table->integer('patient_id', true);
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance');
            $table->string('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('groupe_sanguin', 3)->nullable();
            $table->text('allergies')->nullable();
            $table->text('maladies_chroniques')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('personne_contact', 100)->nullable();
            $table->string('contact_urgence', 20)->nullable();
            $table->string('assurance_medicale', 100)->nullable();
            $table->string('numero_securite_sociale', 50)->nullable();
            $table->integer('localisation_id')->nullable()->index('localisation_id');
            $table->string('lieu_naissance', 100)->nullable();
            $table->string('profession', 100)->nullable();
            $table->string('electrophorese', 50)->nullable();
            $table->text('handicap')->nullable();
            $table->text('vaccination_status')->nullable();
            $table->boolean('donneur_organes')->nullable()->default(false);

            $table->index(['telephone', 'email'], 'idx_patient_contact');
            $table->index(['nom', 'prenom', 'date_naissance'], 'idx_patient_search');
            $table->index(['nom', 'prenom', 'date_naissance', 'localisation_id'], 'idx_patient_search_full');
            $table->unique(['nom', 'prenom', 'date_naissance', 'email'], 'unique_patient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
