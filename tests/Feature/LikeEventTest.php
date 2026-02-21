<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Statistic;
use Livewire\Livewire;

test('guests cannot like an event and need to login', function () {

    $event = Event::factory()->create();
    
    // Will create a statistic record
    $response = $this->get(route('event.show', $event->id));

    // Check if views is 1
    $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'views' => 1,
    ]);

    // Redirect guest to the login route
    Livewire::test('statistic', ['eventId' => $event->id])
        ->call('liked')
        ->assertRedirect(route('login'));

    // Check if likes remains 0
    $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'likes' => 0,
    ]);

});

test('users can like and unlike an event via livewire', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    // Ensure the record doesn't exist
    $this->assertDatabaseMissing('statistics', [
        'event_id' => $event->id,
    ]);

    // Create a statistic record
    $response = $this->get(route('event.show', $event->id));

    // Check if likes remain 0
    $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'likes' => 0,
    ]);

    $component = Livewire::actingAs($user)
        ->test('statistic', ['eventId' => $event->id]);

    // Tests a like 
    $component->call('liked');
    $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'likes' => 1,
    ]);

    // Tests an unlike 
    $component->call('liked');
    $this->assertDatabaseHas('statistics', [
        'event_id' => $event->id,
        'likes' => 0,
    ]);
});

/*
 -------------------------------------------------------------------------
  For testing purposes use the following command
 -------------------------------------------------------------------------
  php artisan test --filter=LikeEventTest

*/

