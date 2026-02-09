<?php

namespace App\Services;

use App\Models\Statistic;
use Carbon\Carbon;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StatisticService
{
   /**
    * Handle the core logic for creating a view statistic.
    */
   public function createNewStatistic(int $eventId): ?Statistic // Statistic or null
   {
      try{
         return Statistic::create([
            'event_id' => $eventId,
         ]);
      }
      catch(\Exception $e){
         report($e);

         return null;
      }
   }

   /**
    * Handle the core logic for updating views statistic.
    */
   public function updateViewStatistic(Statistic $statistic, array $visitors, int $views): ?Statistic
   {
      try{
         $views = $views += 1;
         $succes = $statistic->update([
            'visitor_views'   => $visitors,
            'views'           => $views,
         ]);

         return $succes ? $statistic : null;
      }
      catch(\Exception $e){
         report($e);

         return null;
      }
   }

   /**
    * Handle the core logic for updating views statistic.
    */
   public function updateLikeStatistic(Statistic $statistic, array $users, int $likes, bool $liked = false): ?Statistic
   {
      try
      {
         if($liked) $likes = $likes += 1;
         else $likes = $likes -= 1;

         $succes = $statistic->update([
            'user_likes'   => $users,
            'likes'        => $likes,
         ]);

         return $succes ? $statistic : null;
      }
      catch(\Exception $e)
      {
         report($e);
         return null;
      }
   }

}