<?php

namespace App\Http\Controllers\JobApplications;

use App\Actions\JobApplications\CreateJobApplication;
use App\Enums\JobApplications\DocumentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use Illuminate\Http\RedirectResponse;
use League\Flysystem\UnableToWriteFile;

class JobApplicationStore extends Controller
{
    public function __construct(private readonly CreateJobApplication $createJobApplication) {}

    public function __invoke(StoreJobApplicationRequest $request): RedirectResponse
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
}
