<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CorrectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/application', [ApplicationController::class, 'store']);
Route::post('/correction/request', [CorrectionController::class, 'requestCorrection']);

Route::middleware('statamic.cp.authenticated')->group(function () {
    Route::get('/correction/admin/applications', [CorrectionController::class, 'listApplications']);
    Route::get('/correction/admin/{id}', [CorrectionController::class, 'loadApplicationById']);
    Route::get('/correction/admin/{id}/download/{type}', [CorrectionController::class, 'downloadFileById'])->where('type', 'zip|resume|file');
    Route::post('/correction/admin/{id}', [CorrectionController::class, 'storeCorrectionById']);
});

Route::get('/correction/{token}', [CorrectionController::class, 'loadCorrection']);
Route::get('/correction/{token}/download/{type}', [CorrectionController::class, 'downloadFile'])->where('type', 'zip|resume|file');
Route::post('/correction/{token}', [CorrectionController::class, 'storeCorrection']);
