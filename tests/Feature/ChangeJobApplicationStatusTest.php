<?php

use App\Actions\JobApplications\ChangeJobApplicationStatus;
use App\Enums\JobApplications\Status;
use App\Models\ActivityHistory;
use App\Models\JobApplication;
use Illuminate\Database\QueryException;

test('changing an application status records its previous and new status', function () {
    $application = JobApplication::factory()->create(['status' => Status::Applied]);
    $action = app(ChangeJobApplicationStatus::class);

    $action($application, Status::Interviewing);

    expect($application->fresh()->status)->toBe(Status::Interviewing);
    $history = $application->activityHistory()->sole();
    expect($history->from_status)->toBe(Status::Applied);
    expect($history->to_status)->toBe(Status::Interviewing);
});

test('a failed history insert rolls back the application status change', function () {
    $application = JobApplication::factory()->create(['status' => Status::Applied]);
    $action = app(ChangeJobApplicationStatus::class);
    ActivityHistory::creating(function (ActivityHistory $history): void {
        $history->to_status = null;
    });

    try {
        expect(fn () => $action($application, Status::Interviewing))
            ->toThrow(QueryException::class);
    } finally {
        ActivityHistory::flushEventListeners();
    }

    expect($application->fresh()->status)->toBe(Status::Applied);
    $this->assertDatabaseEmpty('activity_histories');
});
