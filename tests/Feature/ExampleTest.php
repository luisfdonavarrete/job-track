<?php

use App\Models\User;

test('guests visiting the home page are redirected to login', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

test('authenticated users see the dashboard on the home page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()->assertViewIs('dashboard');
});
