<?php

use App\Models\Category;
use App\Models\Event;
use Carbon\CarbonInterface;

it('generates valid event data from the factory', function () {
    // 1. Act: Create a small batch
    $events = Event::factory()->count(10)->create();

    // 2. Assert: Check the structure of the first one
    $event = $events->first();

    expect($event->title)->not->toBeEmpty()
        ->and($event->start_time)->toBeInstanceOf(CarbonInterface::class)
        ->and($event->start_time->isFuture())->toBeTrue()
        ->and($event->category_id)->toBeIn(Category::pluck('id')->toArray())
        ->and($event->content)->not->toBeEmpty()
        ->and($event->status)->not->toBeEmpty();
});
