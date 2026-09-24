<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobApplications\JobApplicationCreate;
use App\Http\Controllers\JobApplications\JobApplicationDestroy;
use App\Http\Controllers\JobApplications\JobApplicationDocumentDownload;
use App\Http\Controllers\JobApplications\JobApplicationEdit;
use App\Http\Controllers\JobApplications\JobApplicationShow;
use App\Http\Controllers\JobApplications\JobApplicationsList;
use App\Http\Controllers\JobApplications\JobApplicationStore;
use App\Http\Controllers\JobApplications\JobApplicationUpdate;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('companies', CompanyController::class);

    Route::prefix('job-applications')->group(function () {
        Route::get('/', JobApplicationsList::class)->name('job-applications.index');
        Route::get('/create', JobApplicationCreate::class)->name('job-applications.create');
        Route::post('/', JobApplicationStore::class)->name('job-applications.store');
        Route::get('/{jobApplication}/show', JobApplicationShow::class)->name('job-applications.show');
        Route::get('/{jobApplication}/edit', JobApplicationEdit::class)->name('job-applications.edit');
        Route::put('/{jobApplication}', JobApplicationUpdate::class)->name('job-applications.update');
        Route::delete('/{jobApplication}', JobApplicationDestroy::class)->name('job-applications.destroy');
        Route::get('/{jobApplication}/documents/{applicationDocument}', JobApplicationDocumentDownload::class)->name('job-applications.document.download');
    });

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/', 'dashboard')->name('home');
});

require __DIR__.'/settings.php';
