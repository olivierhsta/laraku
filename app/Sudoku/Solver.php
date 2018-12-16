<?php

namespace App\Sudoku;
use App\Sudoku\Grid;

class Solver
{

    protected $grid;
    public $full_aglo = [1,2,3,4,5,6,7,8,9];

    public function __construct($grid)
    {
        $this->grid = $grid;
    }

    /**
     * Entry point for the solving algorithm
     * @return Grid       Instante of Sudoku\Grid reprensenting the solved form of the sudoku (if solvable).
     */
    public function solve(){
        $this->write_pencil_marks();
        $this->onechoice();
        $this->write_pencil_marks();
        $this->onechoice();
        return $this->grid->getRows();
    }

    public function onechoice($aglo_name = null)
    {
        foreach ($this->grid->getGrid() as $cell)
        {
            $pencil_marks = $cell->getPencilMarks();
            if ($cell->value == 0 && array_sum($pencil_marks) == 1)
            {
                $cell->value = array_search(1, $pencil_marks);
            }
        }
    }

    public function write_pencil_marks($second=false)
    {
        $one_nine = [1,2,3,4,5,6,7,8,9];
        foreach ($this->grid->getGrid() as $cell)
        {
            $buddies = Grid::getValues($cell->getBuddies());
            if ($second && $cell->row == 1 && $cell->column == 2) dd($cell);
            $cell->setPencilMarks(array_diff($one_nine, $buddies));
        }
    }

}
