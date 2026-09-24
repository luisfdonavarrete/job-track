<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobApplicationDestroy extends Controller
{
    public function __invoke(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        $jobApplication->delete();

        return redirect()->route('job-applications.index')->with('status', 'Job application deleted successfully.');
    }
}
