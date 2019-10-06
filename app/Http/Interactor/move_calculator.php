<?php


namespace App\Http\Interactor;


// use App\Http\Models\Cell;

class move_calculator
{
    private $moveableArray = [];
    private $gameArray = [];
    private $opponentSign = [];
    private $computerSign = [];
    private $boardSize = [];

    public function __construct($gameArray, $opponentSign, $computerSign, $size)
    {
        $this->gameArray = $gameArray;
        $this->opponentSign = $opponentSign;
        $this->computerSign = $computerSign;
        $this->boardSize = $size;
    }


    public function generateMoveableArray()
    {
        $this->checkArrayAndPopulateMoveableArray('row');
        $this->checkArrayAndPopulateMoveableArray('column');
        $this->runThroughForwardDiagonal();
        $this->runThroughBackwardDiagonal();
        return $this->moveableArray;
    }

    private function checkArrayAndPopulateMoveableArray($option)
    {
        $computerCellCount = $opponentCellCount = $freeCellCount = 0;
        $winnable = false;
        $weight = $a = $b = 0;

        for ($i = 0; $i < $this->boardSize; $i++) {
            $computerCellCount = $opponentCellCount = $freeCellCount = 0;
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($option === 'row') {
                    $a = $j;
                    $b = $i;
                } else {
                    $a = $i;
                    $b = $j;
                }

                if ($this->gameArray[$a][$b] === $this->opponentSign) {
                    $opponentCellCount++;
                } else if ($this->gameArray[$a][$b] === $this->computerSign) {
                    $computerCellCount++;
                } else {
                    $freeCellCount++;
                }
            }

            $weight = $this->calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount);

            if ($option === 'column') {
                $this->findFreeColumnCellsAndAssignWeight($a, $weight, $weight === 1);
                print_r("/" . $weight . "\n");
            } else {
                $this->findFreeRowCellsAndAssignWeight($b, $weight, $weight === 1);
            }
        }

        return $winnable;
    }

    private function calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount)
    {
        $weight = 0;
        if ($opponentCellCount === 2 || $computerCellCount === 2) {
            $weight = 3;
        } else if (($opponentCellCount + $computerCellCount) === 2) {
            $weight = 1;
        } else if ($freeCellCount == 3) {
            $weight = 2;
        } else if ($freeCellCount === 0) {
            $weight = 0;
        }
        return $weight;
    }

    private function findFreeColumnCellsAndAssignWeight($i, $weight, $exception)
    {
        for ($j = 0; $j < $this->boardSize; $j++) {
            if ($this->gameArray[$i][$j] === '') {

                $this->pushNewValuesAndUpdateWeight($i, $j, $weight, $exception);

            }
        }
    }

    private function pushNewValuesAndUpdateWeight($x, $y, $weight, $exception)
    {

        $found = false;
        $calculatedWeight = 0;
        for ($i = 0; $i < sizeof($this->moveableArray); $i++) {
            if (($this->moveableArray[$i]->x === $x) && ($this->moveableArray[$i]->y === $y)) {
                $found = true;
                if ($this->moveableArray[$i]->weight < $weight && !$exception) {
                    $this->moveableArray[$i]->weight = $weight;
                    $calculatedWeight = $this->moveableArray[$i]->weight;
                }
            }
        }

        if (!$found) {
            $tempCell = new Cell($x, $y, '', $calculatedWeight);
            array_push($this->moveableArray, $tempCell);
        }
    }

    private function findFreeRowCellsAndAssignWeight($j, $weight, $exception)
    {

        for ($i = 0; $i < $this->boardSize; $i++) {
            print_r($i . " " . $j . " " . $this->gameArray[$i][$j] . "\n");
            if ($this->gameArray[$i][$j] === '') {
                $this->pushNewValuesAndUpdateWeight($i, $j, $weight, $exception);
            }
        }
    }

    private function runThroughForwardDiagonal()
    {
        $computerCellCount = $opponentCellCount = $freeCellCount = 0;
        $winnable = false;
        for ($i = 0; $i < $this->boardSize; $i++) {
            if ($this->gameArray[$i][$i] === $this->opponentSign) {
                $opponentCellCount++;
            } else if ($this->gameArray[$i][$i] === $this->computerSign) {
                $computerCellCount++;
            } else {
                $freeCellCount++;
            }

        }
        $weight = $this->calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount);

        $this->findFreeForwardDiagonalCellsAndAssignWeight($weight, $weight === 1);

        return $winnable;
    }

    private function findFreeForwardDiagonalCellsAndAssignWeight($weight, $exception)
    {
        ;
        for ($i = 0; $i < $this->boardSize; $i++) {
            if ($this->gameArray[$i][$i] === '') {
                $this->pushNewValuesAndUpdateWeight($i, $i, $weight, $exception);
            }
        }
    }

    private function runThroughBackwardDiagonal()
    {
        $computerCellCount = $opponentCellCount = $freeCellCount = 0;
        $winnable = false;
        for ($i = 0, $y = $this->boardSize - 1; $i > -1, $y < $this->boardSize; $y++, $i--) {
            if ($this->gameArray[$i][$y] === $this->opponentSign) {
                $opponentCellCount++;
            } else if ($this->gameArray[$i][$y] === $this->computerSign) {
                $computerCellCount++;
            } else {
                $freeCellCount++;
            }

        }

        $weight = $this->calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount);

        $this->findFreeBackwardDiagonalCellsAndAssignWeight($weight, $weight === 1);
        return $winnable;
    }

    private function findFreeBackwardDiagonalCellsAndAssignWeight($weight, $exception)
    {
        ;
        for ($i = $this->boardSize - 1, $j = 0; $i > -1, $j < $this->boardSize; $i--, $j++) {
            if ($this->gameArray[$i][$j] === '') {
                $this->pushNewValuesAndUpdateWeight($i, $j, $weight, $exception);
            }
        }
    }

}

class Cell
{
    private $x = 0;
    public $y = 0;
    public $weight = 0;
    public $value = '';

    public function __construct($x, $y, $value, $weight)
    {
        $this->x = $x;
        $this->y = $y;
        $this->weight = $weight;
        $this->value = $value;
    }
}

