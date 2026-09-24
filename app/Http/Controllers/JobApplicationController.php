<?php

namespace App\Http\Controllers;

use App\Actions\JobApplications\ChangeJobApplicationStatus;
use App\Actions\JobApplications\CreateJobApplication;
use App\Enums\JobApplications\DocumentType;
use App\Enums\JobApplications\Status;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Models\Company;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use League\Flysystem\UnableToWriteFile;

class JobApplicationController extends Controller
{
    public function __construct(
        private readonly ChangeJobApplicationStatus $changeApplicationStatus,
        private readonly CreateJobApplication $createJobApplication
    ) {}

    public function index(Request $request): View
    {

        return view('job-applications.index', [
            'jobApplications' => JobApplication::forUser($request->user())
                ->with('company')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('job-applications.create', ['companies' => Company::query()->orderBy('name')->get()]);
    }

    public function store(StoreJobApplicationRequest $request): RedirectResponse
    {
        try {
            $jobApplication = ($this->createJobApplication)(
                $request->user(),
                $request->safe()->except(['resume', 'cover_letter']),
                [
                    DocumentType::Resume->value => $request->file('resume'),
                    DocumentType::CoverLetter->value => $request->file('cover_letter'),
                ],
            );
        } catch (UnableToWriteFile $exception) {
            report($exception);

            return redirect()->route('job-applications.create')
                ->withInput($request->safe()->except(['resume', 'cover_letter']))
                ->withErrors(['resume' => __('We could not upload your documents. Please select both files again and try again.')]);
        }

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
        if ($request->user()->cannot('update', $jobApplication)) {
            abort(403);
        }

        return view('job-applications.edit', [
            'jobApplication' => $jobApplication,
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        if ($request->user()->cannot('update', $jobApplication)) {
            abort(403);
        }

        ($this->changeApplicationStatus)(
            $jobApplication,
            Status::from($request->validated('status')),
        );

        $jobApplication->update($request->safe()->except('status'));

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
