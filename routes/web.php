<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

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

// Authenticated route for downloading application ZIP files
Route::middleware('statamic.cp.authenticated')->group(function () {
  Route::get('/applications/{id}/download-zip', [DownloadController::class, 'downloadZip'])
    ->name('applications.download-zip');
});
