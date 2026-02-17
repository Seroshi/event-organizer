<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use App\Services\EventService;

class Event extends Model implements HasMedia
{
   use HasFactory, SoftDeletes, InteractsWithMedia;

   protected $guarded = [];  

   protected $casts = [
      'category_id'  => 'integer',
      'start_time'   => 'datetime:H:i', // option: datetime:H:i
      'end_time'     => 'datetime:H:i',
      'status'       => 'boolean',
   ];

   public function user(): BelongsTo
   {
      return $this->BelongsTo(User::class, 'user_id');
   }

   public function category(): BelongsTo
   {
      return $this->BelongsTo(Category::class, 'category_id');
   }

   public function statistic(): HasOne
   {
      return $this->HasOne(Statistic::class);
   }

   // Spatie Image: the BUCKET rules here
   public function registerMediaCollections(): void
   {
      $this->addMediaCollection('banners')
         ->singleFile() 
         ->withResponsiveImages();
   }

   // Spatie Image: the RESIZING rules here
   public function registerMediaConversions(?Media $media = null): void
   {
      $this->addMediaConversion('thumb')
         ->fit(Fit::Contain, 300, 300)
         ->nonQueued();
   }

   public function getSmartCountdown(): Attribute
   {
      return Attribute::make(
         get: function() {
            return app(EventService::class)->getSmartCountdown($this);
         }
      );
      
   }

   public function isStillActive(): Attribute
   {
      return Attribute::make(
         get: function() {
            $start = $this->start_time;
            $end = $this->end_time;
            
            return ($end) ? $end->gt(now()) : $start->gt(now());
         }
      );
   }

   // Helper function to check if an event has finished 
   public function hasFinished(): Attribute
   {
      return Attribute::make(
         get: function () {
            // If end_time exists, check it; otherwise, check start_time.
            return now()->gt($this->end_time ?? $this->start_time);
         }
      );
      
   }

   public function scopeIsFinished(Builder $query): void
   {
      $query->where(function ($q) {
         $q->whereNotNull('end_time')
            ->where('end_time', '<', now());
      })->orWhere(function ($q) {
         $q->whereNull('end_time')
         ->where('start_time', '<', now());
      });
   }

   public function scopeIsActive(Builder $query): void
   {
      $query->where(function ($q) {
         $q->whereNotNull('end_time')
            ->where('end_time', '>', now());
      })->orWhere(function ($q) {
         $q->whereNull('end_time')
         ->where('start_time', '>', now());
      });
   }
}
