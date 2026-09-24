<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

final class ApplicationDocumentStorage
{
    public static function disk(): FilesystemAdapter
    {
        $disk = Storage::disk(
            config('job-applications.documents_disk', 'local'),
        );

        return $disk;
    }
}
