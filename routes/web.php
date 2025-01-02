<?php

use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/upload', [LabelController::class, 'uploadform']);

Route::post('/generate-labels', [LabelController::class, 'generateLabels']);

Route::get('/download/{file}', function ($file) {
    $filePath = 'generated_files/' . $file;

    if (Storage::exists($filePath)) {
        return Storage::download($filePath);
    }

    abort(404, 'File not found.');
})->name('download.file');

require __DIR__.'/auth.php';
