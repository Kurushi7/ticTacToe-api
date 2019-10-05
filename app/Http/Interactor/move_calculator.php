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
        print_r($this->moveableArray);
        $this->checkArrayAndPopulateMoveableArray('column');
        $this->runThroughForwardDiagonal();
        $this->runThroughBackwardDiagonal();
        return $this->moveableArray;
    }

    public function checkArrayAndPopulateMoveableArray($option)
    {
        $computerCellCount = $opponentCellCount = $freeCellCount = 0;
        $winnable = false;
        $weight = $a = $b = 0;

        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($option === 'row') {
                    $a = $i;
                    $b = $j;
                } else {
                    $a = $j;
                    $b = $i;
                }
                print_r("$this->gameArray[" . $a . "][" . $b . "]= " . $this->gameArray[$a][$b] . "\n");

                if ($this->gameArray[$a][$b] === $this->opponentSign) {
                    $opponentCellCount++;
                } else if ($this->gameArray[$a][$b] === $this->computerSign) {
                    $computerCellCount++;
                } else {
                    $freeCellCount++;
                }
            }

            $weight = $this->calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount);

            if ($option === 'row') {
                $this->findFreeRowCellsAndAssignWeight($this->gameArray[$i], $j - 1, $weight);
            } else {
                $this->findFreeColumnCellsAndAssignWeight($j, $weight);
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

    public function findFreeRowCellsAndAssignWeight($row, $j, $weight)
    {
        ;
        for ($i = 0; $i < $this->boardSize; $i++) {
            if ($row[$i] === '') {
                $tempCell = new Cell($i, $j, $weight);
                array_push($this->moveableArray, $tempCell);
            }
        }
    }

    public function findFreeColumnCellsAndAssignWeight($i, $weight)
    {
        ;
        for ($j = 0; $j < $this->boardSize; $j++) {
            if ($this->gameArray[$i][$j] === '') {
                $tempCell = new Cell($i, $j, $weight);
                array_push($this->moveableArray, $tempCell);
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

        $this->findFreeForwardDiagonalCellsAndAssignWeight($weight);

        return $winnable;
    }

    public function findFreeForwardDiagonalCellsAndAssignWeight($weight)
    {
        ;
        for ($i = 0; $i < $this->boardSize; $i++) {
            if ($this->gameArray[$i][$i] === '') {
                $tempCell = new Cell($i, $i, $weight);
                array_push($this->moveableArray, $tempCell);
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

        $this->findFreeBackwardDiagonalCellsAndAssignWeight($weight);
        return $winnable;
    }

    public function findFreeBackwardDiagonalCellsAndAssignWeight($weight)
    {
        ;
        for ($i = $this->boardSize - 1, $j = 0; $i > -1, $j < $this->boardSize; $i--, $j++) {
            if ($this->gameArray[$i][$j] === '') {
                $tempCell = new Cell($i, $j, $weight);
                array_push($this->moveableArray, $tempCell);
            }
        }
    }

}

