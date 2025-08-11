<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AppointmentStatusController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\PredeterminedPricesController;
use App\Http\Controllers\Api\TherapistController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UbigeoController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\ImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
*/

// Route::get('/user', function (Request $request) {
//    return $request->user();
// })->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {

    // 📁 Document Types
    Route::prefix('document-types')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index']);
        Route::post('/', [DocumentTypeController::class, 'store']);
        Route::get('/{documentType}', [DocumentTypeController::class, 'show']);
        Route::patch('/{documentType}', [DocumentTypeController::class, 'update']);
        Route::delete('/{documentType}', [DocumentTypeController::class, 'destroy']);
    });

    // 💳 Payment Types
    Route::prefix('payment-types')->group(function () {
        Route::get('/', [PaymentTypeController::class, 'index']);
        Route::post('/', [PaymentTypeController::class, 'store']);
        Route::get('/{paymentType}', [PaymentTypeController::class, 'show']);
        Route::patch('/{paymentType}', [PaymentTypeController::class, 'update']);
        Route::delete('/{paymentType}', [PaymentTypeController::class, 'destroy']);
    });

    // 💳 Predetermined Prices
    Route::prefix('predetermined-prices')->group(function () {
        Route::get('/', [PredeterminedPricesController::class, 'index']);
        Route::post('/', action: [PredeterminedPricesController::class, 'store']);
        Route::get('/{predeterminedPrice}', [PredeterminedPricesController::class, 'show']);
        Route::patch('/{predeterminedPrice}', [PredeterminedPricesController::class, 'update']);
        Route::delete('/{predeterminedPrice}', [PredeterminedPricesController::class, 'destroy']);
    });

    // 📅 Appointment Statuses
    Route::prefix('appointment-statuses')->group(function () {
        Route::get('/', [AppointmentStatusController::class, 'index']);
        Route::post('/', [AppointmentStatusController::class, 'store']);
        Route::get('/{appointmentStatus}', [AppointmentStatusController::class, 'show']);
        Route::patch('/{appointmentStatus}', [AppointmentStatusController::class, 'update']);
        Route::delete('/{appointmentStatus}', [AppointmentStatusController::class, 'destroy']);
    });

    // 👥 Patients
    Route::prefix('patients')->group(function () {
        Route::get('/search', [PatientController::class, 'searchPatients']);
        Route::get('/', [PatientController::class, 'index']);
        Route::post('/', [PatientController::class, 'store']);
        Route::get('/{patient}', [PatientController::class, 'show']);
        Route::patch('/{patient}', [PatientController::class, 'update']);
        Route::delete('/{patient}', [PatientController::class, 'destroy']);
        Route::get('appoiments/{patient}', [PatientController::class, 'getAppointmentsByPatient']);
    });

    // 👨‍⚕️ Therapists
    Route::prefix('therapists')->group(function () {
        Route::get('/search', [TherapistController::class, 'searchTherapists']);
        Route::get('/', [TherapistController::class, 'index']);
        Route::post('/', [TherapistController::class, 'store']);
        Route::get('/{therapist}', [TherapistController::class, 'show']);
        Route::patch('/{therapist}', [TherapistController::class, 'update']);
        Route::delete('/{therapist}', [TherapistController::class, 'destroy']);
    });

    // 📖 Histories
    Route::prefix('histories')->group(function () {
        Route::get('/', [HistoryController::class, 'index']);
        Route::post('/', [HistoryController::class, 'store']);
        Route::get('/{history}', [HistoryController::class, 'show']);
        Route::patch('/{history}', [HistoryController::class, 'update']);
        Route::delete('/{history}', [HistoryController::class, 'destroy']);
        
        Route::get('/patient/{patient}', [HistoryController::class, 'getByPatient']);
    });

    // 📆 Appointments
    Route::prefix('appointments')->group(function () {
        Route::get('/paginated', [AppointmentController::class, 'getPaginatedAppointmentsByDate']);
        Route::get('/completed', [AppointmentController::class, 'getCompletedAppointmentsPaginatedByDate']);
        Route::get('/calendar/pending', [AppointmentController::class, 'getPendingAppointmentsForCalendarByDate']);
        Route::get('/calendar/completed', [AppointmentController::class, 'getCompletedAppointmentsForCalendarByDate']);
        Route::get('/search', [AppointmentController::class, 'searchAppointments']);
        Route::get('/completed/search', [AppointmentController::class, 'searchCompletedAppointments']);

        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/{appointment}', [AppointmentController::class, 'show']);
        Route::patch('/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy']);
    });

    // 📊 Reports
    Route::prefix('report')->group(function () {
        Route::get('/appointmentsForTherapist', [ReportController::class, 'getNumberAppointmentsPerTherapist']);
        Route::get('/patientsByTherapist', [ReportController::class, 'getPatientsByTherapist']);
        Route::get('/dailyCash', [ReportController::class, 'getDailyCash']);
        Route::get('/appointmentsBetweenDates', [ReportController::class, 'getAppointmentsBetweenDates']);
        Route::get('/statistics', [StatisticsController::class, 'getStatistics']);
    });

    // 👤 Users
    Route::prefix('users')->group(function () {
        Route::get('/search', [UserController::class, 'searchUsers']);
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);

        // Las rutas protegidas deben ir antes de las dinámicas
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/photo', [UserController::class, 'uploadPhoto'])->name('users.photo.upload');
            Route::patch('/photo', [UserController::class, 'updatePhoto'])->name('users.photo.update');
            Route::get('/photo', [UserController::class, 'showPhoto'])->name('users.photo.show');
            Route::delete('/photo', [UserController::class, 'deletePhoto'])->name('users.photo.delete');
        });

        // Rutas dinámicas al final
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::patch('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });
    // Rutas específicas para fotos de usuarios - SIMPLIFICADAS

    // Rutas para manejo de datos de la empresa
    Route::prefix('company')->group(function () {
        Route::get('/', [CompanyController::class, 'show'])->name('company.show');
        Route::post('/', [CompanyController::class, 'store'])->name('company.store');

        // Rutas para manejo del logo
        Route::post('/logo', [CompanyController::class, 'uploadLogo'])->name('company.logo.upload');
        Route::get('/logo', [CompanyController::class, 'showLogo'])->name('company.logo.show');
        Route::delete('/logo', [CompanyController::class, 'deleteLogo'])->name('company.logo.delete');
    });
});

// 📍 Ubigeo
Route::prefix('ubigeo')->group(function () {
    Route::get('/regions', [UbigeoController::class, 'regions']);
    Route::get('/provinces/{regionId}', [UbigeoController::class, 'provinces']);
    Route::get('/districts/{provinceId}', [UbigeoController::class, 'districts']);
    Route::get('/countries', [UbigeoController::class, 'countries']);
});
