<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/upload', [FileUploadController::class, 'uploadForm']);

Route::post('/upload', [FileUploadController::class, 'processUpload']);

require __DIR__.'/auth.php';
