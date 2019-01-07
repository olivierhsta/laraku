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
     *
     * @return array[] Value found by the algorithm
     *                  ex.  $found[0] = [
     *                          "cell" => "13",
     *                          "method" => "One Choice",
     *                          "action" => "Contains",
     *                          "values" => 8
     *                      ];
     */
    public function gridSolve()
    {
        return $this->groupSolve($this->grid->getGrid());
    }

    /**
     * Executes the one choice algorithm on a given group.
     *
     * For every cell in the group, we check if it has only one pencil mark, 
     * if it does, we replace the value of the cell by the value of this
     * pencil mark
     *
     * @return array[] Value found by the algorithm
     *                  ex.  $found[0] = [
     *                          "cell" => "13",
     *                          "method" => "One Choice",
     *                          "action" => "Contains",
     *                          "values" => 8
     *                      ];
     */
    public function groupSolve(array $group)
    {
        $found = array();
        foreach ($group as $cell)
        {
            $pencilMarks = $cell->getPencilMarks();
            if ($cell->isEmpty() && sizeof($pencilMarks) == 1)
            {
                $cell->setValue($pencilMarks[0]);
                $found[] = [
                    "cell" => $cell->row . $cell->col,
                    "method" => "One Choice",
                    "action" => "Places",
                    "values" => $cell->getValue()
                ];
            }
        }
        return $found;
    }

}
