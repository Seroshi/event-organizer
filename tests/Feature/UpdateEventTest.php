<?php

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

test('update old event dates to the future', function () {

    Carbon::setTestNow('21-02-2026 12:00');

    $eventA = Event::factory()->create([
        'title' => 'Bergen',
        'start_time' => now()->subDays(25)->startOfMinute(),
        'end_time' => now()->subDays(25)->startOfMinute(),
    ]);

    $eventB = Event::factory()->create([
        'title' => 'Stenen',
        'start_time' => now()->subDays(2)->startOfMinute(),
        'end_time' => now()->subDays(2)->startOfMinute(),
    ]);

    $fixedDateC = now()->subDays(20)->startOfMinute();
    $eventC = Event::factory()->create([
        'title' => 'Wolken',
        'start_time' => $fixedDateC,
        'end_time' => $fixedDateC,
    ]);

    $fixedDateD = now()->addDays(2)->startOfMinute();
    $eventD = Event::factory()->create([
        'title' => 'Zonnestraal',
        'start_time' => $fixedDateD,
        'end_time' => $fixedDateD,
    ]);

    // Get all past events
    $pastEvents = Event::where('start_time', '<', now())
        ->orderBy('start_time', 'asc')
        ->pluck('id');

    // Decide how many past events should be shown
    $limit = 2;

    if( $pastEvents->count() > $limit)
    {
        //Move the collected past events 
        Event::whereIn('id', $pastEvents)->get()->each(function ($event) use ($limit, $pastEvents)
        {
            // Update end time if exist, otherwise start time
            if($event->end_time){
                $weeksAgo = ceil( $event->end_time->diffInWeeks() );
            }
            else{
                $weeksAgo = ceil( $event->start_time->diffInWeeks() );
            }

            // All events over the limit
            $groupFuture = $pastEvents->slice(0, -$limit);

            // Move this event to the future
            if($groupFuture->contains($event->id))
            {
                $total = $weeksAgo + 4;
            }
            // Update this event a bit if too old but keep in past events display
            else
            {
                if($weeksAgo > 1){
                    $total = $weeksAgo - 1;
                }
            }

            // Update the events dates in the database
            $event->update([
                'start_time' => $event->start_time->addWeeks($total ?? 0),
                'end_time' => $event->end_time?->addWeeks($total ?? 0),
            ]);
        });
    }

    expect($eventA->fresh()->start_time->isFuture())->toBeTrue(); 
    expect($eventB->fresh()->start_time->isPast())->toBeTrue(); 
    expect($eventC->fresh()->start_time->equalTo($fixedDateC))->toBeFalse();
    expect($eventC->fresh()->start_time->isPast())->toBeTrue();
    expect($eventD->fresh()->start_time->equalTo($fixedDateD))->toBeTrue();
});

/*
 -------------------------------------------------------------------------
  For testing purposes use the following command
 -------------------------------------------------------------------------
  php artisan test --filter=UpdateEventTest

*/