<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'game_id',
        'secret_word',
        'attempts',
        'won'
    ];
}
