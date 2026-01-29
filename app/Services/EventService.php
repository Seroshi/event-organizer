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
        'category_id' => (int) $data['category_id'],
        'start_time'  => Carbon::parse($data['start_time']),
        'content'     => $data['content'],
        'status'      => (bool) ($data['status'] ?? false),
      ]);
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

   public function getSmartCountdown(Event $event): string
   {
      $now = now();
      $start = $event->start_time;
      $past = $now->greaterThan($start);
      $text = ($past) ? ' geleden' : ' te gaan'; 

      return $start->locale('nl')->diffForHumans($now, true) . $text;
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