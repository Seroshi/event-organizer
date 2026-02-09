<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
