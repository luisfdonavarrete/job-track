<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobApplicationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('companies', CompanyController::class);

    Route::prefix('job-applications')->group(function () {
        Route::get('/', [JobApplicationController::class, 'index'])->name('job-applications.index');
        Route::get('/create', [JobApplicationController::class, 'create'])->name('job-applications.create');
        Route::post('/', [JobApplicationController::class, 'store'])->name('job-applications.store');
        Route::get('/{job_application}/show', [JobApplicationController::class, 'show'])->name('job-applications.show');
        Route::get('/{job_application}/edit', [JobApplicationController::class, 'edit'])->name('job-applications.edit');
        Route::put('/{job_application}', [JobApplicationController::class, 'update'])->name('job-applications.update');
        Route::delete('/{job_application}', [JobApplicationController::class, 'destroy'])->name('job-applications.destroy');
        Route::resource('{job_application}/documents', JobApplicationController::class);
    });

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/', 'dashboard')->name('home');
});

require __DIR__.'/settings.php';
