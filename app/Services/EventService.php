<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventService
{
   /**
    * Handle the core logic for creating an event.
    */
   public function createEvent(array $data, $image = null): Event
   {
      // 1. Process the image if it exists
      if ($image) {
         $data['image'] = $image->store('events', 'public');
      }

      // 2. Ensure the date is a Carbon instance 
      $data['start_time'] = Carbon::parse($data['start_time']);

      // 3. Create the record in the database
      return Event::create($data);
   }

   /**
    * Handle the core logic for updating an event.
    */
   public function updateEvent(Event $event, array $data): Event
   {

      // 1. Ensure the date is a Carbon instance 
      $data['start_time'] = Carbon::parse($data['start_time']);

      // 2. Update the record in the database [note: tap() to ensure a model return]
      return tap($event)->update($data);
   }

   /**
    * Logic for calculating time remaining (useful for your TS countdown)
    */
   public function getTimeRemaining(Event $event): array
   {
      $diff = now()->diff($event->starts_at);

      return [
         'days'    => $diff->d,
         'hours'   => $diff->h,
         'minutes' => $diff->i,
         'seconds' => $diff->s,
         'is_past' => now()->greaterThan($event->starts_at),
      ];
   }
}