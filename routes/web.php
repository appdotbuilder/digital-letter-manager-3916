<?php

use App\Http\Controllers\DigitalSignatureController;
use App\Http\Controllers\LetterApprovalController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LetterRejectionController;
use App\Http\Controllers\LetterSubmissionController;
use App\Http\Controllers\SignatureVerificationController;
use App\Http\Controllers\UserKeyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Public signature verification  
Route::get('/verify', [SignatureVerificationController::class, 'index'])->name('signatures.verify');
Route::post('/verify', [SignatureVerificationController::class, 'store'])->name('signatures.verify.check');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Letter management routes
    Route::resource('letters', LetterController::class);
    Route::post('letters/{letter}/submit', [LetterSubmissionController::class, 'store'])->name('letters.submit');
    Route::post('letters/{letter}/approve', [LetterApprovalController::class, 'store'])->name('letters.approve');
    Route::post('letters/{letter}/reject', [LetterRejectionController::class, 'store'])->name('letters.reject');

    // Digital signature routes
    Route::get('letters/{letter}/sign', [DigitalSignatureController::class, 'create'])->name('signatures.create');
    Route::post('letters/{letter}/sign', [DigitalSignatureController::class, 'store'])->name('signatures.store');
    Route::get('signatures/{signature}', [DigitalSignatureController::class, 'show'])->name('signatures.show');

    // User key management routes
    Route::resource('user-keys', UserKeyController::class)->except(['edit']);
    Route::post('user-keys/{userKey}/test', [UserKeyController::class, 'update'])->name('user-keys.test');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
