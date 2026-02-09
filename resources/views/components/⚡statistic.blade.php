<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Services\StatisticService;
use App\Models\Statistic;
use App\Models\Event;
use Carbon\Carbon;

new class extends Component
{
   public $eventId;

   public function mount(): void
   {
   }

   // Find the corresponding statistic based on Event HasOne relationship
   #[Computed] 
   public function statistic(): ?Statistic //return null if not found
   {
      $eventId = $this->eventId;

      $event = Event::findOrFail($eventId);

      $statistic = $event->statistic;

      if($statistic)
      {
         return $statistic;
      }
      else
      {
         $createStatistic = app(StatisticService::class)->createNewStatistic($eventId);

         if(!$createStatistic){
            session()->flash('error', 'Er is iets misgegaan bij registreren van een weergave.');
            return null;
         }else{
            return $createStatistic;
         }
      }
   }

   #[Computed]
   public function getViewCount(): int
   {
      // Look for existing statistic record
      $statistic = $this->statistic();

      // Update statistics if found
      if($statistic)
      {
         // Get view count
         $viewCount = (int) $statistic->views;

         // Get current session ID 
         $sessionId = session()->getId();

         // Get visitors array from existing record
         $visitorsArray = (array) $statistic->visitor_views;

         // Check if the current session exist in the record
         $exists = collect($visitorsArray)->contains('sessionId', $sessionId);
         
         // Update visitors array if a new visitor is found
         if(!$exists)
         {
            // 1. Define session data with the correct types
            $sessionData[] = [
               'sessionId' => (string) $sessionId,
               'date'      => Carbon::now()->format('d-m-y H:i:s'),
            ];

            // 2. Create the new array with all items combined
            $newVisitorsArray = [...$visitorsArray, ...$sessionData];

            // 3. Update the statistic record
            $updateStatistic = app(StatisticService::class)->updateViewStatistic($statistic, $newVisitorsArray, $viewCount);
            
            // 4. Send error message if fail or update view count if success 
            if(!$updateStatistic){
               session()->flash('error', 'Er is iets misgegaan bij het bijwerken van een weergave.');
            }else{
               $viewCount = (int) $statistic->views;
            }
         }

         return $viewCount;
      }
   }

   public function liked()
   {
      $statistic = $this->statistic();

      if($statistic){

         $userId = auth()->user()?->id;

         if(!$userId)
         {
            return dd('Please log in');
         }
         else
         {
            // Get visitors array from existing record
            $likesArray = (array) $statistic->user_likes;

            $likeCount = (int) $statistic->likes;

            // Check if the current session exist in the record
            $exists = collect($likesArray)->contains('userId', $userId);

            $newLikesArray = [];

            // Update likes array if a new user has liked
            if($exists){

               $liked = false;

               $newLikesArray = collect($likesArray)->where('userId', '!=', $userId)->all();

            }
            else
            {
               $liked = true;

               // 2 Define likes data with the correct types
               $likeData[] = [
                  'userId' => (int) $userId,
                  'date'   => Carbon::now()->format('d-m-y H:i:s'),
               ];

               // 3. Create the new array with all items combined
               $newLikesArray = [...$likesArray, ...$likeData];
            }

            $updateStatistic = app(StatisticService::class)->updateLikeStatistic($statistic, $newLikesArray, $likeCount, $liked);

            if(!$updateStatistic){
               session()->flash('error', 'Er is iets misgegaan bij het bijwerken van een like.');
            }else{
               $likeCount = (int) $statistic->likes;
            }
         }
      }
   }

   #[Computed] 
   public function getLikeState()
   {
      $statistic = $this->statistic();

      if($statistic){
         $userId = auth()->user()?->id;

         // Get visitors array from existing record
         $likesArray = (array) $statistic->user_likes;

         // Check if the current session exist in the record
         $exists = collect($likesArray)->contains('userId', $userId);

         return $exists ? 'solid' : 'outline';

      }else
      {
         return 'outline'; 
      }
   }

   #[Computed] 
   public function getLikesCount(): int
   {
      $statistic = $this->statistic();

      if($statistic)
      {
         return $statistic->likes;
      }
      else
      {
         return 0; 
      }
   }

};
?>

<section class="flex gap-5 items-center">
   <div class="flex gap-1 items-center">
      <div wire:click="liked" class="p-1 cursor-pointer">
         <flux:icon.heart variant="{{ $this->getLikeState() }}" class="size-7 sm:size-6" />
      </div>
      <span class="text-xl font-medium">{{ $this->getLikesCount() }}</span>
   </div>
   <div class="flex gap-1 items-center">
      <span>Weergaven:</span>
      <span class="text-xl font-medium">{{ $this->getViewCount }}</span>
   </div>
</section>