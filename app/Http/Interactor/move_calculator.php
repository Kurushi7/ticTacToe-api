<?php


namespace App\Http\Interactor;


use App\Models\Cell;
use App\Repositories\Game_moves;
use Illuminate\Http\Request;

/**
 * Class move_calculator
 * @package App\Http\Interactor
 */
class move_calculator
{
    private $moveableArray = [];
    private $gameArray = [];
    private $opponentSign;
    private $computerSign;
    private $boardSize = [];
    private $playerWon = false;
    private $computerWon = false;
    private $finalMovesArray = [];
    protected $movesRepository;
    protected $gamesRepository;

    protected $request;

    public function __construct()
    {
        $this->request = app(Request::class);
        $this->movesRepository = app(Game_moves::class);
    }

    public function getBestRoutes()
    {

        $params = $this->request->json()->all();

//        $this->gameArray = [ //uncomment and comment next line to send a static predefined array.
//            ['', '', ''],
//            ['X', 'O', ''],
//            ['X', 'X', '']];
        $this->gameArray = $params['array']; //

        $this->opponentSign = $params['opponentSign'];
        $this->computerSign = $params['computerSign'];
        $this->boardSize = $params['size'];

        if (sizeof($this->gameArray) === 0) {
            $x = rand(0, $this->boardSize);
            $y = rand(0, $this->boardSize);
            $gameId = $this->gamesRepository->saveGame('pending', $this->computerSign, 'undecided');
            $this->movesRepository->insertMove($x, $y, 2, $gameId);
            return new Cell(rand(0, $this->boardSize), rand(0, $this->boardSize), $this->computerSign, 2);
        }


        $this->generateMoveableArray();
        $maxValue = max(array_column($this->moveableArray, 'weight'));

        for ($i = 0; $i < sizeof($this->moveableArray); $i++) {
            if ($this->moveableArray[$i]->weight === $maxValue) {
                array_push($this->finalMovesArray, $this->moveableArray[$i]);
            }
        }
        $random_value = array_rand($this->finalMovesArray);

        $this->movesRepository->insertMove($this->finalMovesArray[$random_value]->x, $this->finalMovesArray[$random_value]->y, $this->finalMovesArray[$random_value]->weight, false);
        return $this->finalMovesArray[$random_value];
    }

    private function generateMoveableArray()
    {
        $this->checkArrayAndPopulateMoveableArray('row');
        $this->checkArrayAndPopulateMoveableArray('column');
        $this->runThroughForwardDiagonal();
        $this->runThroughBackwardDiagonal();
    }


    private function checkArrayAndPopulateMoveableArray($option)
    {
        $computerCellCount = $opponentCellCount = $freeCellCount = 0;
        $winnable = false;


        for ($i = 0; $i < $this->boardSize; $i++) {
            $weight = $a = $b = 0;
            $computerCellCount = $opponentCellCount = $freeCellCount = 0;

            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($option === 'row') {
                    $a = $i;
                    $b = $j;
                } else {
                    $a = $j;
                    $b = $i;
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

            if ($option === 'row') {
                $this->findFreeRowCellsAndAssignWeight($a, $weight, $weight === 1);

            } else {
                ;
                $this->findFreeColumnCellsAndAssignWeight($b, $weight, $weight === 1);
            }

        }

        return $winnable;
    }

    private function calculateWeight($computerCellCount, $opponentCellCount, $freeCellCount)
    {

        $this->checkIfGameWon($opponentCellCount, $computerCellCount);

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

    private function checkIfGameWon($opponentCellCount, $computerCellCount)
    {
        if ($opponentCellCount === 3) {
            $this->playerWon = true;
        }
        if ($computerCellCount === 2) { // the next move will always be a winning one for the computer player
            $this->computerWon = true;
        }
    }

    private function findFreeRowCellsAndAssignWeight($i, $weight, $exception)
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
        $calculatedWeight = $weight;
        if (sizeof($this->moveableArray) !== 0) {
            for ($i = 0; $i < sizeof($this->moveableArray); $i++) {
                if (($this->moveableArray[$i]->x === $x) && ($this->moveableArray[$i]->y === $y)) {
                    $found = true;
                    if ($this->moveableArray[$i]->weight < $weight && !$exception) {

                        $this->moveableArray[$i]->weight = $weight;

                        $calculatedWeight = $this->moveableArray[$i]->weight;

                    }
                }
            }
        }

        if (!$found) {
            $tempCell = new Cell($x, $y, '', $calculatedWeight);
            array_push($this->moveableArray, $tempCell);

        }
    }

    private function findFreeColumnCellsAndAssignWeight($j, $weight, $exception)
    {
        for ($i = 0; $i < $this->boardSize; $i++) {

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

        for ($i = $this->boardSize - 1, $j = 0; $i > -1, $j < $this->boardSize; $i--, $j++) {
            if ($this->gameArray[$i][$j] === '') {
                $this->pushNewValuesAndUpdateWeight($i, $j, $weight, $exception);
            }
        }
    }
}
