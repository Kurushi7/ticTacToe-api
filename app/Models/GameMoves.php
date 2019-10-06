<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameMoves extends Model
{
    public $timestamps = true;
    protected $table = 'game_moves';
    protected $fillable = ['gameId', 'row', 'column', 'mark'];
}
