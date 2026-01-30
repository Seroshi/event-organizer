<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\EventService;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit; // New in v3/v11
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $guarded = [];  

    protected $casts = [
        'category_id' => 'integer',
        'start_time' => 'datetime:H:i', // option: datetime:H:i
        'end_time' => 'datetime:H:i',
        'status'     => 'boolean',
    ];

    public function category()
    {
        return $this->BelongsTo(Category::class, 'category_id');
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
}
