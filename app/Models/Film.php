<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'character_film');
    }
}
