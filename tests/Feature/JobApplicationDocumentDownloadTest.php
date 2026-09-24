<?php

use App\Models\ApplicationDocument;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function documentForApplication(JobApplication $application, string $type = 'resume'): ApplicationDocument
{
    return $application->documents()->create([
        'type' => $type,
        'path' => 'application-documents/'.$type.'.pdf',
        'original_filename' => $type.'.pdf',
        'mime_type' => 'application/pdf',
    ]);
}

test('owners download the original file from private storage', function () {
    $disk = Storage::fake('local');
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    $application = JobApplication::factory()->create();
    $document = documentForApplication($application);
    $disk->put($document->path, 'Private document contents');

    $this->actingAs($application->user)
        ->get(route('job-applications.document.download', [$application, $document]))
        ->assertDownload('resume.pdf')
        ->assertStreamedContent('Private document contents');
});

test('guests must sign in to download documents', function () {
    $application = JobApplication::factory()->create();
    $document = documentForApplication($application);

    $this->get(route('job-applications.document.download', [$application, $document]))
        ->assertRedirect(route('login'));
});

test('other users cannot download application documents', function () {
    $application = JobApplication::factory()->create();
    $document = documentForApplication($application);

    $this->actingAs(User::factory()->create())
        ->get(route('job-applications.document.download', [$application, $document]))
        ->assertForbidden();
});

test('documents from another application cannot be downloaded through an owned application', function () {
    $application = JobApplication::factory()->create();
    $document = documentForApplication(JobApplication::factory()->create());

    $this->actingAs($application->user)
        ->get(route('job-applications.document.download', [$application, $document]))
        ->assertForbidden();
});

test('missing stored files return not found', function () {
    Storage::fake('local');
    $application = JobApplication::factory()->create();
    $document = documentForApplication($application);

    $this->actingAs($application->user)
        ->get(route('job-applications.document.download', [$application, $document]))
        ->assertNotFound();
});

test('missing document records return not found', function () {
    $application = JobApplication::factory()->create();

    $this->actingAs($application->user)
        ->get(route('job-applications.document.download', [$application, 999]))
        ->assertNotFound();
});

test('missing applications return not found for downloads', function () {
    $application = JobApplication::factory()->create();
    $document = documentForApplication($application);

    $this->actingAs($application->user)
        ->get(route('job-applications.document.download', ['00000000-0000-4000-8000-000000000000', $document]))
        ->assertNotFound();
});

test('application details show document labels filenames and download links', function () {
    $application = JobApplication::factory()->create();
    $resume = documentForApplication($application);
    $cover = documentForApplication($application, 'cover_letter');
    $resume->update(['original_filename' => '<script>alert(1)</script>.pdf']);

    $this->actingAs($application->user)->get(route('job-applications.show', $application))
        ->assertSee('Download Resume')
        ->assertSee('Download Cover letter')
        ->assertSee('<script>alert(1)</script>.pdf')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('cover_letter.pdf')
        ->assertSee(route('job-applications.document.download', [$application, $resume]))
        ->assertSee(route('job-applications.document.download', [$application, $cover]));
});

test('application details explain when no documents have been uploaded', function () {
    $application = JobApplication::factory()->create();

    $this->actingAs($application->user)->get(route('job-applications.show', $application))
        ->assertSee('No documents uploaded.');
});
