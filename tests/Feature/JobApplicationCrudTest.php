<?php

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;

test('guests cannot access application actions', function (string $method, string $action) {
    $application = JobApplication::factory()->create();
    $this->{$method}(route('job-applications.'.$action, $application))->assertRedirect(route('login'));
})->with([['get', 'index'], ['get', 'create'], ['get', 'show'], ['get', 'edit'], ['post', 'store'], ['put', 'update'], ['delete', 'destroy']]);

test('application pages render for their owner', function (string $action) {
    $application = JobApplication::factory()->create();
    $this->actingAs($application->user)->get(route('job-applications.'.$action, $application))
        ->assertOk()->assertViewIs('job-applications.'.$action);
})->with(['index', 'create', 'show', 'edit']);

test('the index only lists the current users applications', function () {
    $own = JobApplication::factory()->create(['title' => 'Visible application']);
    JobApplication::factory()->create(['title' => 'Private application']);
    $this->actingAs($own->user)->get(route('job-applications.index'))
        ->assertSee('Visible application')->assertDontSee('Private application');
});

test('other users cannot access or modify an application', function (string $method, string $action) {
    $application = JobApplication::factory()->create();
    $original = $application->fresh()->getAttributes();
    $this->actingAs(User::factory()->create())->{$method}(route('job-applications.'.$action, $application))
        ->assertForbidden();
    expect($application->fresh()->getAttributes())->toBe($original);
})->with([['get', 'show'], ['get', 'edit'], ['put', 'update'], ['delete', 'destroy']]);

test('creation uses the authenticated owner and persists application fields', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create();
    $this->actingAs($user)->post(route('job-applications.store'), [
        'title' => 'Developer', 'company_id' => $company->id, 'status' => 'applied',
        'user_id' => User::factory()->create()->id, 'job_url' => 'https://example.com/job',
        'salary_min' => 50000, 'salary_max' => 60000, 'applied_at' => '2026-09-14T12:00',
        'follow_up_at' => '2026-09-21T12:00', 'location' => 'Toronto', 'description' => 'Example role',
    ])->assertSessionHasNoErrors()->assertRedirect(route('job-applications.show', JobApplication::sole()));
    $this->assertDatabaseHas('job_applications', ['title' => 'Developer', 'user_id' => $user->id, 'company_id' => $company->id, 'salary_min' => 50000, 'salary_max' => 60000, 'location' => 'Toronto', 'description' => 'Example role']);
    expect(JobApplication::sole()->applied_at->format('Y-m-d H:i'))->toBe('2026-09-14 12:00');
});

test('updates can change companies and clear optional values without transferring ownership', function () {
    $application = JobApplication::factory()->create();
    $company = Company::factory()->create();
    $this->actingAs($application->user)->put(route('job-applications.update', $application), [
        'title' => 'Updated role', 'company_id' => $company->id, 'status' => 'offered',
        'user_id' => User::factory()->create()->id, 'salary_min' => null, 'salary_max' => null,
        'applied_at' => null, 'follow_up_at' => null,
    ])->assertSessionHasNoErrors()->assertRedirect(route('job-applications.show', $application));
    $this->assertDatabaseHas('job_applications', ['id' => $application->id, 'title' => 'Updated role', 'company_id' => $company->id, 'user_id' => $application->user_id, 'status' => 'offered', 'salary_min' => null, 'salary_max' => null, 'applied_at' => null, 'follow_up_at' => null]);
});

test('invalid application fields are rejected without changing data', function (array $invalid, string $field) {
    $application = JobApplication::factory()->create();
    $original = $application->fresh()->getAttributes();
    $payload = array_merge(['title' => 'Valid role', 'company_id' => $application->company_id, 'status' => 'applied'], $invalid);
    $this->actingAs($application->user);
    $this->post(route('job-applications.store'), $payload)->assertSessionHasErrors($field);
    $this->put(route('job-applications.update', $application), $payload)->assertSessionHasErrors($field);
    $this->assertDatabaseCount('job_applications', 1);
    expect($application->fresh()->getAttributes())->toBe($original);
})->with([
    [['title' => ''], 'title'], [['title' => str_repeat('x', 256)], 'title'],
    [['company_id' => 'not-a-uuid'], 'company_id'],
    [['company_id' => '00000000-0000-4000-8000-000000000000'], 'company_id'],
    [['status' => 'unknown'], 'status'], [['job_url' => 'javascript:alert(1)'], 'job_url'],
    [['salary_min' => -1], 'salary_min'], [['salary_max' => 100000000], 'salary_max'],
    [['salary_min' => 100, 'salary_max' => 50], 'salary_max'],
    [['applied_at' => 'invalid'], 'applied_at'], [['follow_up_at' => 'invalid'], 'follow_up_at'],
]);

test('maximum salary does not require a minimum salary', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('job-applications.store'), [
        'title' => 'Role', 'company_id' => Company::factory()->create()->id,
        'status' => 'applied', 'salary_min' => null, 'salary_max' => 60000,
    ])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('job_applications', ['salary_min' => null, 'salary_max' => 60000]);
});

test('owners can delete applications without deleting the company', function () {
    $application = JobApplication::factory()->create();
    $company = $application->company;
    $this->actingAs($application->user)->delete(route('job-applications.destroy', $application))
        ->assertRedirect(route('job-applications.index'));
    $this->assertModelMissing($application);
    $this->assertModelExists($company);
});

test('missing applications return not found', function (string $method, string $action) {
    $this->actingAs(User::factory()->create())->{$method}(route('job-applications.'.$action, '00000000-0000-4000-8000-000000000000'))->assertNotFound();
})->with([['get', 'show'], ['get', 'edit'], ['put', 'update'], ['delete', 'destroy']]);
