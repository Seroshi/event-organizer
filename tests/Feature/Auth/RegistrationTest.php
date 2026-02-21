<?php

use App\Models\Profile;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('corresponding profile is made upon registry', function () {
    $user = \App\Models\User::factory()
        ->has(Profile::factory())
        ->create([
            'id' => 4,
            'name' => 'John May',
            'email' => 'test@test.nl',
            'password' => 'password',
        ]);

    $profile = Profile::firstOrFail();

    // Check if profile has the correct user id
    expect($profile->user_id)->toBe($user->id);
});



/*
 -------------------------------------------------------------------------
  For testing purposes use the following command
 -------------------------------------------------------------------------
  php artisan test --filter=RegistrationTest

*/