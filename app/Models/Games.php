<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    public $timestamps = true;
    protected $table = 'games';
    protected $fillable = ['status', 'mark', 'player'];

    public function gameMoves()
    {
        return $this->hasMany('App\Models\GameMoves');
    }
}
