<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JobApplicationEdit extends Controller
{
    public function __invoke(Request $request, JobApplication $jobApplication): View
    {
        if ($request->user()->cannot('update', $jobApplication)) {
            abort(403);
        }

        return view('job-applications.edit', [
            'jobApplication' => $jobApplication,
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }
}
