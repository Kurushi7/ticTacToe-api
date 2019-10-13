<?php


namespace App\Http\Interactor;


use App\Models\Games;
use App\Repositories\Game_moves;
use Illuminate\Http\Request;

class player
{
    protected $gamesRepository;
    protected $movesRepository;
    protected $request;
    protected $row;
    protected $column;
    protected $mark;
    protected $boardSize;
    protected $new;

    public function __construct()
    {
        $this->request = app(Request::class);
        $this->movesRepository = app(Game_moves::class);
        $this->gamesRepository = app(Games::class);
    }

    public function insertMove()
    {
        $params = $this->request->json()->all();
        $gameId = null;
        $this->row = $params['row'];
        $this->column = $params['column'];
        $this->mark = $params['mark'];
        $this->boardSize = $params['size'];
        $this->new = $params['new'];

        if ($this->new) {
            $gameId = $this->gamesRepository->saveGame('pending', $this->mark, 'undecided');
        }

        $this->movesRepository->insertMove($this->row, $this->column, 2, $gameId);
        return ["success" => true];
    }
}
