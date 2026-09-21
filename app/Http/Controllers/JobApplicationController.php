<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Models\Company;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(Request $request): View
    {

        return view('job-applications.index', [
            'jobApplications' => JobApplication::forUser($request->user())
            ->with('company')
            ->latest()
            ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        return view('job-applications.create', ['companies' => Company::query()->orderBy('name')->get()]);
    }

    public function store(StoreJobApplicationRequest $request): RedirectResponse
    {
        $jobApplication = $request->user()->jobApplications()->create($request->validated());

        return redirect()->route('job-applications.show', $jobApplication)->with('status', 'Job application created successfully.');
    }

    public function show(Request $request, JobApplication $jobApplication): View
    {
    
        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        return view('job-applications.show', ['jobApplication' => $jobApplication->load('company')]);
    }

    public function edit(Request $request, JobApplication $jobApplication): View
    {
        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        return view('job-applications.edit', [
            'jobApplication' => $jobApplication,
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        $jobApplication->update($request->validated());

        return redirect()->route('job-applications.show', $jobApplication)->with('status', 'Job application updated successfully.');
    }

    public function destroy(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        if ($request->user()->cannot('view', $jobApplication)) {
            abort(403);
        }

        $jobApplication->delete();

        return redirect()->route('job-applications.index')->with('status', 'Job application deleted successfully.');
    }
}
