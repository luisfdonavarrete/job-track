<?php

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;

test('guests cannot access company actions', function (string $method, string $action) {
    $company = Company::factory()->create();

    $this->{$method}(route('companies.'.$action, $company))->assertRedirect(route('login'));
})->with([['get', 'index'], ['get', 'create'], ['get', 'show'], ['get', 'edit'], ['post', 'store'], ['put', 'update'], ['delete', 'destroy']]);

test('company pages render', function (string $action) {
    $company = Company::factory()->create(['name' => 'Example Company']);

    $this->actingAs(User::factory()->create())->get(route('companies.'.$action, $company))
        ->assertOk()->assertViewIs('companies.'.$action);
})->with(['index', 'create', 'show', 'edit']);

test('users can create a company with only validated fields', function () {
    $this->actingAs(User::factory()->create())->post(route('companies.store'), [
        'name' => 'Example Company', 'website' => 'https://example.com',
        'location' => 'Toronto', 'description' => 'A company.', 'id' => 'untrusted-id',
    ])->assertSessionHasNoErrors()->assertRedirect(route('companies.show', Company::sole()))
        ->assertSessionHas('status', 'Company created successfully.');

    $this->assertDatabaseHas('companies', ['name' => 'Example Company', 'website' => 'https://example.com', 'location' => 'Toronto', 'description' => 'A company.']);
    expect(Company::sole()->id)->not->toBe('untrusted-id');
});

test('users can update a company and clear optional values', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->create())->put(route('companies.update', $company), [
        'name' => 'Updated Company', 'website' => null, 'location' => null, 'description' => null,
    ])->assertSessionHasNoErrors()->assertRedirect(route('companies.show', $company));

    $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Updated Company', 'website' => null, 'location' => null, 'description' => null]);
});

test('invalid company input does not change records', function (string $field, mixed $value) {
    $company = Company::factory()->create();
    $original = $company->fresh()->getAttributes();
    $payload = ['name' => 'Valid name', $field => $value];
    $this->actingAs(User::factory()->create());

    $this->post(route('companies.store'), $payload)->assertSessionHasErrors($field);
    $this->put(route('companies.update', $company), $payload)->assertSessionHasErrors($field);

    $this->assertDatabaseCount('companies', 1);
    expect($company->fresh()->getAttributes())->toBe($original);
})->with([
    ['name', ''], ['name', str_repeat('a', 256)], ['name', ['invalid']],
    ['website', 'javascript:alert(1)'], ['website', 'https://example.com/'.str_repeat('a', 256)],
    ['location', str_repeat('a', 256)], ['location', ['invalid']], ['description', ['invalid']],
]);

test('users can delete a company without applications', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->create())->delete(route('companies.destroy', $company))
        ->assertRedirect(route('companies.index'))->assertSessionHasNoErrors();

    $this->assertModelMissing($company);
});

test('companies with applications cannot be deleted', function () {
    $application = JobApplication::factory()->create();
    $company = $application->company;

    $this->actingAs(User::factory()->create())->delete(route('companies.destroy', $company))
        ->assertRedirect(route('companies.show', $company))
        ->assertSessionHasErrors(['company' => 'This company has job applications and cannot be deleted.']);

    $this->assertModelExists($company);
    $this->assertModelExists($application);
});

test('missing companies return not found', function (string $method, string $action) {
    $this->actingAs(User::factory()->create())->{$method}(route('companies.'.$action, '00000000-0000-4000-8000-000000000000'))
        ->assertNotFound();
})->with([['get', 'show'], ['get', 'edit'], ['put', 'update'], ['delete', 'destroy']]);
