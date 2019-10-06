<?php

namespace App\repositories;

class Game_moves
{
    private $model;

    public function __construct(Game_moves $model)
    {
        $this->model = $model;
    }

    public function insertMove($row, $column, $mark)
    {
        //not gonna put any kind of validation here
        $this->model->row = $row;
        $this->model->column = $column;
        $this->model->mark = $mark;
        $this->model->gameId = $this->getLastGameId();
        $this->model->save();
    }

    private function getLastGameId()
    {
        return $this->model::max('gameId');

    }
}
