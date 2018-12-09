<?php

namespace App;
use App\Sudoku\Grid;

class Solver
{

    /**
     * Entry point for the solving algorithm
     * @param  String $grid Encoding of the grid.  Expected to be 81 char
     * @return Srting       Encoding of the solved grid (if solvable).
     */
    public function solve($grid){
        $grid = new Grid($grid);
        return $grid->getRows();
    }

}
