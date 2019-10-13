<?php

namespace App\Http\Controllers;


use App\Http\Interactor\move_calculator;

class GameController extends Controller
{
    protected $calculator;

    /**
     * GameController constructor.
     */
    public function __construct()
    {
//        $array = [
//            ['', '', ''],
//            ['X', 'O', ''],
//            ['X', 'X', '']];
//        $this->calculator = new move_calculator($array, 'X', 'O', 3);
        $this->calculator = app(move_calculator::class);
    }

    public function __invoke()
    {

        $result = $this->calculator->getBestRoutes();
        return response()->json($result);
    }
}
