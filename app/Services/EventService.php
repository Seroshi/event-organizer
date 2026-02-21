<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EventService
{
   /**
    * Handle the core logic for creating an event.
    */
   public function createEvent(array $data): Event
   {

      // Create the record in the database and ensure the correct types
      return Event::create([
        'title'       => $data['title'],
        'user_id'     => auth()->user()->id,
        'category_id' => (int) $data['category_id'],
        'start_time'  => Carbon::parse($data['start_time']),
        'end_time'    => ($data['end_time']) ? Carbon::parse($data['end_time']) : null,
        'content'     => $data['content'],
        'status'      => (bool) ($data['status'] ?? false),
      ]);
   }

   /**
    * Handle the core logic for updating an event.
    */
   public function updateEvent(Event $event, array $data): Event
   {

      // Update the record in the database [note: tap() to ensure a model return]
      return tap($event)->update([
         'title'       => $data['title'],
         'category_id' => (int) $data['category_id'],
         'start_time'  => Carbon::parse($data['start_time']),
         'end_time'    => ($data['end_time']) ? Carbon::parse($data['end_time']) : null,
         'content'     => $data['content'],
         'status'      => (bool) ($data['status'] ?? false),
      ]);
   }

   /**
    * Handle the core logic for soft and force deleting an event.
    */
   public function delete(Event $event, $force = false): bool{

      try{
         return $event->delete();
      } catch (\Exception $e){
         report($e);
         return false;
      }

      return $force ? $event->forceDelete() : $event->delete();
   }

   /**
    * Handle the core logic for restoring an event.
    */
   public function restore(Event $event): bool{
      try{
         return $event->restore();
      } catch (\Exception $e){
         report($e);
         return false;
      }
   }

   /**
    * Handle the logic for displaying the Event's amount of time left 
    */
   public function getSmartCountdown(Event $event): string
   {
      $now = now();
      $start = $event->start_time;
      $end = $event->end_time;
      $past = ($end) ? $now->greaterThan($end) : $now->greaterThan($start);
      $text = ($past) ? ' geleden' : ' te gaan'; 

      // Check if event is ungoing
      if($now->greaterThan($start) && !$now->greaterThan($end)){
         return 'Nu aan de gang';
      }

      // Show countdown from now 
      if($end && $past) {
         return $end->diffForHumans($now, true) . $text;
      }
      else {
         return $start->diffForHumans($now, true) . $text;
      }
   }
   
   /**
    * Handle the logic for uploading an image with Spatie Media.
    */
   public function uploadImage(Event $event, $file): media
   {
      // Spatie handles the TemporaryUploadedFile automatically
      return $event->addMedia($file)->toMediaCollection('banners');
   }
   
   
}