<?php

namespace App\Repositories;

class Games
{
    private $model;

    public function __construct()
    {
        $this->model = app(\App\Models\Games::class);
    }

    public function saveGame($phone_number, $status, $mark, $player)
    {
        $this->model->fill(array('phone_number' => $phone_number,
            'status' => $status,
            'mark' => $mark,
            'player' => $player));
        $this->model->save();
        return $this->model->id;
    }


}
