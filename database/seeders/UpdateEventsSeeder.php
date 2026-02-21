<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Event;
use Carbon\Carbon;

class UpdateEventsSeeder extends Seeder
{
   /**
   * Logic to update old events dates to balance the events display
   */
   public function run(): void
   {
      // Get all past events
    $pastEvents = Event::where('start_time', '<', now())
        ->orderBy('start_time', 'asc')
        ->pluck('id');

    // Decide how many past events should be shown
    $limit = 3;

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
            try{
               $event->update([
                  'start_time' => $event->start_time->addWeeks($total ?? 0),
                  'end_time' => $event->end_time?->addWeeks($total ?? 0),
               ]);
            }
            catch(\Exception $e){
               report($e);
               session()->flash('error', 'Er iets misgegaan met de bijwerking van events data');
            }
         });
      }

      // php artisan db:seed --class=UpdateEventsSeeder
   }
}
