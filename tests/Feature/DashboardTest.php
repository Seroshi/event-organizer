<?php

use App\Models\User;
use App\Models\Profile;
use App\Models\Event;
use App\Models\Statistic;
use Livewire\Livewire;


test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('authenticated users can change their profile info', function () {
    $user = User::factory()
        ->has(Profile::factory()->state(['company' => 'Acme Corp']))
        ->create(['name' => 'John Bingo', 'email' => 'j.b@test.nl']);
    
    $response = Livewire::actingAs($user)
        ->test('dashboard-profile')
        ->call('getProfileData')
        ->set('profileName', 'Rick Keizer')
        ->set('profileEmail', 'j.bingo@test.nl')
        ->set('profileCompany', 'Ijzer Koning')
        ->call('profileSave');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Rick Keizer');
    expect($user->email)->toBe('j.bingo@test.nl');
    expect($user->profile->company)->toBe('Ijzer Koning');
});

test('admin can view other users and change user roles', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $userA = User::factory()->create(['role' => 'user']);
    $userB = User::factory()->create(['role' => 'organizer']);

    $component = Livewire::actingAs($admin)
        ->test('dashboard-user');

    // Admin can see all users overview
    $component->assertSee('Alle gebruikers');

    $component->call('getUserData', $userA->id)
        ->set('userRole', 'organizer')
        ->call('save');

    $component->call('getUserData', $userB->id)
        ->set('userRole', 'admin')
        ->call('save');

    $userA->refresh();
    $userB->refresh();

    expect($userA->role->value)->toBe('organizer');
    expect($userB->role->value)->toBe('admin');
});

test('authenticated users can change their statistics order', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('dashboard-stats')
        ->assertStatus(403);

    $organizer = User::factory()->create(['role' => 'organizer']);

    $highLikeEvent = Event::factory()->has(Statistic::factory([
            'views' => 50,
            'likes' => 30,
        ]))
        ->create(['user_id' => $organizer->id]);

    $highViewEvent = Event::factory()->has(Statistic::factory([
            'views' => 100,
            'likes' => 20,
        ]))
        ->create(['user_id' => $organizer->id]);

    // Aim is to keep this event in the middle 
    $regularEvent = Event::factory()->has(Statistic::factory([
            'views' => 65,
            'likes' => 10,
        ]))
        ->create(['user_id' => $organizer->id]);

    $component = Livewire::actingAs($organizer)
        ->test('dashboard-stats');

    // Check if highest like is at the top
    $component->set('order', 'asc')
        ->call('toggleSort', 'likes')
        ->assertSet('allEvents', function ($events) use ($highLikeEvent) {
            expect($events->first()->id)->toBe($highLikeEvent->id);
            return true;
        });

    // Check if highest like is at the bottom
    $component->set('order', 'desc')
        ->call('toggleSort', 'likes')
        ->assertSet('allEvents', function ($events) use ($highLikeEvent) {
            expect($events->last()->id)->toBe($highLikeEvent->id);
            return true;
        });

    // Check if highest view is at the top
    $component->set('order', 'asc')
        ->call('toggleSort', 'views')
        ->assertSet('allEvents', function ($events) use ($highViewEvent) {
            expect($events->first()->id)->toBe($highViewEvent->id);
            return true;
        });

    // Check if highest view is at the bottom
    $component->set('order', 'desc')
        ->call('toggleSort', 'views')
        ->assertSet('allEvents', function ($events) use ($highViewEvent) {
            expect($events->last()->id)->toBe($highViewEvent->id);
            return true;
        });
});

test('authenticated users can view their liked events', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    // Redirect guest to the login route
    Livewire::actingAs($user)
        ->test('statistic', ['eventId' => $event->id])
        ->call('liked');

    $record = $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'likes' => 1,
        'views' => 1,
    ]);

    Livewire::actingAs($user)
        ->test('dashboard-likes')
        ->assertSet('allEvents', function ($events) use ($event) {
            expect($events)->not->toBeEmpty();
            expect($events->count())->toBe(1);
            return true;
        });
});

/*
 -------------------------------------------------------------------------
  For testing purposes use the following command
 -------------------------------------------------------------------------
  php artisan test --filter=DashboardTest

*/
