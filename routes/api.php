<?php

declare(strict_types=1);

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobs')->as('jobs.')->group(function () {
    Route::post('/', [JobController::class, 'store'])->name('store');
    Route::get('/{id}', [JobController::class, 'show'])->name('show');
});
