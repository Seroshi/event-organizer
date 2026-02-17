<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = []; 

    public const TYPES = [
        'Actie', 
        'Anime', 
        'Cultuur', 
        'Eten', 
        'Educatie', 
        'Entertainment', 
        'Games', 
        'Geschiedenis', 
        'Kunst',
        'Musea', 
        'Music', 
        'Musical', 
        'Familie', 
        'Social', 
        'Sport', 
        'Technologie',
        'Workshop'
    ];

    public function event(){
        return $this->hasMany(Event::class);
    }
}
