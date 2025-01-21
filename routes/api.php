<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\MedicalController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\CarteUrgenceController;
use App\Http\Controllers\API\VaccinationController;
use App\Http\Controllers\API\RendezVousController;
use App\Http\Controllers\API\LocalisationController;
use App\Http\Controllers\API\CarnetMaternitéController;


/*
|--------------------------------------------------------------------------|
|                            API Routes                                    |
|--------------------------------------------------------------------------|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// routes/api.php
Route::prefix('v1')->middleware(['api.key'])->group(function () {
    // Auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

        // api roles
    Route::get('auth/roles/register', [AuthController::class, 'getRegistrationRoles']);
    
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('auth/user-profile', [AuthController::class, 'getUserProfile']);
    Route::put('auth/update-user-profile', [AuthController::class, 'updateUserProfile']);

    // Routes protégées par authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'user']);
        

        // Routes pour les patients (protégées par rôle)
        Route::middleware(['role:Admin,Médecin'])->group(function () {
            Route::apiResource('patients', PatientController::class);
            Route::get('patients/search', [PatientController::class, 'search']);
        });

        // Routes médicales
        Route::middleware(['role:Admin,Médecin,Infirmier'])->group(function () {
            // Consultations
            Route::get('medical/records/{patientId}', [MedicalController::class, 'getRecords']);
            Route::get('medical/consultations/{patientId}', [MedicalController::class, 'getConsultations']);
            Route::post('medical/consultations', [MedicalController::class, 'storeConsultation']);
            
            // Examens
            Route::get('medical/examinations/{patientId}', [MedicalController::class, 'getExaminations']);
            Route::post('medical/examinations', [MedicalController::class, 'storeExamination']);
        });

        // Documents
        Route::middleware(['role:Admin,Médecin'])->group(function () {
            Route::post('documents/upload', [DocumentController::class, 'upload']);
            Route::get('documents/{id}', [DocumentController::class, 'download']);
            Route::get('documents', [DocumentController::class, 'index']);
        });

        // Carte d'urgence (accès plus large pour les urgences)
        Route::middleware(['role:Admin,Médecin,Infirmier,Secouriste'])->group(function () {
            Route::get('cartes-urgence/{patientId}', [CarteUrgenceController::class, 'show']);
            Route::middleware(['role:Admin,Médecin'])->group(function () {
                Route::post('cartes-urgence', [CarteUrgenceController::class, 'store']);
                Route::put('cartes-urgence/{id}', [CarteUrgenceController::class, 'update']);
            });
        });

        // Vaccinations
        Route::middleware(['role:Admin,Médecin,Infirmier'])->group(function () {
            Route::apiResource('vaccinations', VaccinationController::class);
        });

        // Rendez-vous
        Route::middleware(['role:Admin,Médecin'])->group(function () {
            Route::apiResource('rendez-vous', RendezVousController::class);
        });

        // Localisation
        Route::middleware(['role:Admin'])->group(function () {
            Route::apiResource('localisations', LocalisationController::class);
            Route::get('localisations/{id}/statistics', [LocalisationController::class, 'statistics']);
        });

        // Carnet de maternité
        Route::middleware(['role:Admin,Médecin'])->group(function () {
            Route::apiResource('carnets-maternite', CarnetMaternitéController::class);
            Route::put('carnets-maternite/{id}/terminer', [CarnetMaternitéController::class, 'terminer']);
            Route::get('carnets-maternite/statistics', [CarnetMaternitéController::class, 'statistics']);
        });
    });
});