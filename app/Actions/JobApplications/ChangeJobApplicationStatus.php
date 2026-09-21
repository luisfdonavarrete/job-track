<?php

namespace App\Actions\JobApplications;

use App\Enums\JobApplications\Status;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;

final class ChangeJobApplicationStatus
{
    public function __invoke(JobApplication $jobApplication, Status $newStatus): void
    {
        DB::transaction(function () use ($jobApplication, $newStatus): void {
            $oldStatus = $jobApplication->status;

            $jobApplication->update(['status' => $newStatus]);

            $jobApplication->activityHistory()->create([
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
            ]);
        });
    }
}
