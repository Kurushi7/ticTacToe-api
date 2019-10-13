<?php

namespace App\Repositories;

use App\Models\GameMoves;

class Game_moves
{
    private $model;

    public function __construct()
    {
        $this->model = app(GameMoves::class);
    }

    public function insertMove($row, $column, $mark, $id = null)
    {
        //not gonna put any kind of validation here
        $this->model->row = $row;
        $this->model->column = $column;
        $this->model->mark = $mark;
        $this->model->game_Id = $id === null ? $this->getLastGameId() + 1 : $id;
        $this->model->save();
    }

    private function getLastGameId()
    {
        $gameId = $this->model::max('gameId');
        return $gameId === NULL ? 1 : $gameId;

    }

}
