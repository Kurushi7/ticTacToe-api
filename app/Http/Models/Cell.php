<?php

namespace App\Http\Models;


class Cell
{
    private $x = 0;
    private $y = 0;
    private $value = '';
    private $weight = 0;

    public function __construct($x, $y, $value, $weight)
    {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;
        $this->weight = $weight;
    }
}
