<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

/**
 * Class to solve one choice problems.
 *
 * If a cell only as one pencil mark (aka one possible number can fill it),
 * the value is set to the cell
 *
 */
class OneChoiceSolver extends Solver
{

    protected $grid;

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
    }

    /**
     * Executes the one choice algorithm on the whole grid.
     *
     * For every cell in the grid, we check if it has only one pencil mark,
     * if it does, we replace the value of the cell by the value of this
     * pencil mark
     */
    public function solve()
    {
        foreach ($this->grid->getGrid() as $cell)
        {
            $pencilMarks = $cell->getPencilMarks();
            if ($cell->isEmpty() && sizeof($pencilMarks) == 1)
            {
                $cell->setValue($pencilMarks[0]);
                $this->markMove($cell);
            }
        }
    }

    /**
     * Creates an entry in the $found array.
     *  The structure is
     *      "cell"   => [cell position],
     *      "method" => "One Choice",
     *      "values" => [placed number],
     *      "grid"   => [grid encoding]
     *
     * @param  Cell $cell        affected cell
     */
    public function markMove($cell, $pencilMarks=null, $args=array()) {
        $this->found[] = [
            "cell"   => $cell->row() . $cell->col(),
            "method" => "One Choice",
            "values" => $cell->getValue(),
            "grid"   => $this->grid->encoding(),
        ];
    }
}
