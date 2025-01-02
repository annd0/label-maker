<?php

use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/upload', [LabelController::class, 'uploadform']);

Route::post('/generate-labels', [LabelController::class, 'generateLabels']);

require __DIR__.'/auth.php';
