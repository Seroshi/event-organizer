<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\EventService;

class Event extends Model
{
    use SoftDeletes;

    protected $guarded = [];  

    protected $casts = [
        'category_id' => 'integer',
        'start_time' => 'datetime:H:i', // option: datetime:H:i
        'status'     => 'boolean',
    ];

    public function category()
    {
        return $this->BelongsTo(Category::class, 'category_id');
    }
}
