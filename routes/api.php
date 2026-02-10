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
Route::get('/correction/{token}', [CorrectionController::class, 'loadCorrection']);
Route::get('/correction/{token}/download/{type}', [CorrectionController::class, 'downloadFile'])->where('type', 'zip|resume|file');
Route::post('/correction/{token}', [CorrectionController::class, 'storeCorrection']);
