<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMoves extends Model
{
    public $timestamps = true;
    protected $table = 'game_moves';
    protected $fillable = ['game_Id', 'row', 'column', 'mark'];

    public function games()
    {
        return $this->belongsTo('App\Models\Games');
    }
}
