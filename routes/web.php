<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobApplicationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('companies', CompanyController::class);
    Route::resource('job-applications', JobApplicationController::class);

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/', 'dashboard')->name('home');
});

require __DIR__.'/settings.php';
