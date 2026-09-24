<?php

namespace App\Actions\JobApplications;

use App\Models\JobApplication;
use App\Models\User;
use App\Support\ApplicationDocumentStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use RuntimeException;
use Throwable;

class CreateJobApplication
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array{resume: UploadedFile, cover_letter: UploadedFile}  $documents
     */
    public function __invoke(User $user, array $attributes, array $documents): JobApplication
    {
        $disk = ApplicationDocumentStorage::disk();
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($user, $attributes, $documents, $disk, &$storedPaths): JobApplication {
                $jobApplication = $user->jobApplications()->create($attributes);

                foreach ($documents as $type => $file) {
                    $path = $disk->putFile('application-documents', $file);

                    if ($path === false) {
                        throw UnableToWriteFile::atLocation('application-documents');
                    }

                    $storedPaths[] = $path;

                    $jobApplication->documents()->create([
                        'type' => $type,
                        'original_filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                    ]);
                }

                return $jobApplication;
            });
        } catch (Throwable $exception) {
            try {
                if ($storedPaths !== [] && ! $disk->delete($storedPaths)) {
                    throw new RuntimeException('Could not clean up job application documents.');
                }
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }
}
