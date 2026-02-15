<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Number;

class Statistic extends Model
{
    protected $guarded = [];  
    
    protected function casts(): array
    {
        return [
            'visitor_views'     => 'json', 
            'user_likes'        => 'json',
            'user_followers'    => 'json',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->BelongsTo(Event::class, 'event_id');
    }

    // For abbreviating the views numbers (11234 -> 11K)
    protected function viewsFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => Number::abbreviate($this->views),
        );
    }

    // For abbreviating the likes numbers (11234 -> 11K)
    protected function likesFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => Number::abbreviate($this->likes),
        );
    }
}
