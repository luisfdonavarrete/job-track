<?php

use App\Models\ApplicationDocument;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;

function applicationUploadPayload(): array
{
    return [
        'title' => 'Developer',
        'company_id' => Company::factory()->create()->id,
        'status' => 'applied',
        'resume' => UploadedFile::fake()->create('resume.pdf', 10, 'application/pdf'),
        'cover_letter' => UploadedFile::fake()->create('cover.pdf', 10, 'application/pdf'),
    ];
}

test('creation saves both documents privately even when the default disk differs', function () {
    $disk = Storage::fake('local');
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    $user = User::factory()->create();
    $payload = applicationUploadPayload();

    $this->actingAs($user)->post(route('job-applications.store'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('job-applications.show', JobApplication::sole()));

    $this->assertDatabaseCount('application_documents', 2);
    foreach (['resume' => 'resume.pdf', 'cover_letter' => 'cover.pdf'] as $type => $name) {
        $document = ApplicationDocument::where('type', $type)->sole();
        expect($document->original_filename)->toBe($name);
        expect($document->mime_type)->toBe('application/pdf');
        expect($document->job_application_id)->toBe(JobApplication::sole()->id);
        $disk->assertExists($document->path);
    }
    Storage::disk('public')->assertDirectoryEmpty('/');
});

test('upload failures roll back records and files and preserve form text', function (bool $throws) {
    $disk = Storage::fake('local');
    $storage = Mockery::mock($disk);
    $calls = 0;
    $storage->shouldReceive('putFile')->andReturnUsing(function ($directory, $file) use ($disk, &$calls, $throws) {
        if (++$calls === 2) {
            if ($throws) {
                throw UnableToWriteFile::atLocation('private storage detail');
            }

            return false;
        }

        return $disk->putFile($directory, $file);
    });
    Storage::set('local', $storage);
    Exceptions::fake();
    $payload = applicationUploadPayload();

    $this->actingAs(User::factory()->create())->post(route('job-applications.store'), $payload)
        ->assertRedirect(route('job-applications.create'))
        ->assertSessionHasErrors(['resume' => 'We could not upload your documents. Please select both files again and try again.'])
        ->assertSessionHasInput('title', 'Developer')
        ->assertSessionMissing('_old_input.resume');

    $this->assertDatabaseCount('job_applications', 0);
    $this->assertDatabaseCount('application_documents', 0);
    $disk->assertDirectoryEmpty('application-documents');
    Exceptions::assertReported(UnableToWriteFile::class);
    Exceptions::assertReportedCount(1);
})->with(['false return' => false, 'exception' => true]);

test('unexpected failures return a friendly 500 and clean up uploads', function () {
    $disk = Storage::fake('local');
    config(['app.debug' => false]);
    Exceptions::fake();
    $payload = applicationUploadPayload();
    ApplicationDocument::creating(function (): void {
        throw new RuntimeException('Sensitive database detail');
    });

    $this->actingAs(User::factory()->create())->post(route('job-applications.store'), $payload)
        ->assertInternalServerError()
        ->assertSee('We could not complete your request. Please try again later.')
        ->assertDontSee('Sensitive database detail');

    $this->assertDatabaseCount('job_applications', 0);
    $this->assertDatabaseCount('application_documents', 0);
    $disk->assertDirectoryEmpty('application-documents');
    Exceptions::assertReported(RuntimeException::class);
    Exceptions::assertReportedCount(1);
});

test('cleanup failures are reported without hiding the original failure', function (bool $throws) {
    $disk = Storage::fake('local');
    $storage = Mockery::mock($disk);
    if ($throws) {
        $storage->shouldReceive('delete')->andThrow(new RuntimeException('Cleanup failed'));
    } else {
        $storage->shouldReceive('delete')->andReturn(false);
    }
    Storage::set('local', $storage);
    Exceptions::fake();
    config(['app.debug' => false]);
    $payload = applicationUploadPayload();
    $original = new LogicException('Original failure');
    ApplicationDocument::creating(function () use ($original): void {
        throw $original;
    });

    $this->actingAs(User::factory()->create())->post(route('job-applications.store'), $payload)
        ->assertInternalServerError();

    $this->assertDatabaseCount('job_applications', 0);
    $this->assertDatabaseCount('application_documents', 0);
    Exceptions::assertReported(fn (LogicException $exception) => $exception === $original);
    Exceptions::assertReported(RuntimeException::class);
    Exceptions::assertReportedCount(2);
})->with(['false return' => false, 'exception' => true]);
