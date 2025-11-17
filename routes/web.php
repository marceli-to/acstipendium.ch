<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public route for downloading application resume/dossier files
Route::get('/applications/{id}/download-resume', [DownloadController::class, 'downloadPublicResume'])
    ->name('applications.download-resume-public');

// Protected route for downloading application ZIP files (authenticated users only)
Route::middleware('statamic.cp.authenticated')->group(function () {
    Route::get('/applications/{id}/download-zip', [DownloadController::class, 'downloadProtectedZip'])
        ->name('applications.download-zip-protected');
});
