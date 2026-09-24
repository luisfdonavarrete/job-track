<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\JobApplication;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationDocumentDownload extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        JobApplication $jobApplication,
        ApplicationDocument $applicationDocument
    ): StreamedResponse {

        if (
            $request->user()->cannot('view', $jobApplication) ||
            $applicationDocument->job_application_id !== $jobApplication->id
        ) {
            abort(403);
        }
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless($disk->exists($applicationDocument->path), 404);

        return $disk->download(
            $applicationDocument->path,
            $applicationDocument->original_filename,
        );
    }
}
