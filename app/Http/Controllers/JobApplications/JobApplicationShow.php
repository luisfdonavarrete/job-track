<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JobApplicationShow extends Controller
{
    public function __invoke(Request $request, JobApplication $jobApplication): View
    {

        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        return view('job-applications.show', ['jobApplication' => $jobApplication->load('company', 'documents')]);
    }
}
