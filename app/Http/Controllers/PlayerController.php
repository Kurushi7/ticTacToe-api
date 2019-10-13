<?php

namespace App\Http\Controllers;

use App\Http\Interactor\player;

class PlayerController extends Controller
{
    protected $player;

    /**
     * PlayeController constructor.
     */
    public function __construct()
    {
        $this->player = app(player::class);
    }

    public function __invoke()
    {

        $result = $this->player->insertMove();
        return response()->json($result);
    }
}
