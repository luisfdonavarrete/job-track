<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JobApplicationsList extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('job-applications.index', [
            'jobApplications' => JobApplication::forUser($request->user())
                ->with('company')
                ->latest()
                ->paginate(10),
        ]);
    }
}
