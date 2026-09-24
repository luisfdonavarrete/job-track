<?php

namespace App\Http\Controllers\JobApplications;

use App\Actions\JobApplications\ChangeJobApplicationStatus;
use App\Enums\JobApplications\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;

class JobApplicationUpdate extends Controller
{
    public function __construct(private readonly ChangeJobApplicationStatus $changeApplicationStatus) {}

    public function __invoke(UpdateJobApplicationRequest $request, JobApplication $jobApplication): RedirectResponse
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
}
