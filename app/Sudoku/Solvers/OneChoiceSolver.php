<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

class OneChoiceSolver extends Solver
{

    protected $grid;

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
    }

    /**
     * Executes the one choice algorithm on the whole grid
     *
     * If a cell only as one pencil mark (aka one possible number can fill it),
     * the value is set to the cell
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found[0] = [
     *          "cell" => "13",
     *          "method" => "One Choice",
     *          "action" => "Contains",
     *          "values" => 8
     *      ];
     */
    public function grid_solve()
    {
        return $this->group_solve($this->grid->get_grid());
    }

    public function group_solve(array $group)
    {
        $found = array();
        foreach ($group as $cell)
        {
            $pencil_marks = $cell->get_pencil_marks();
            if ($cell->is_empty() && sizeof($pencil_marks) == 1)
            {
                $cell->set_value($pencil_marks[0]);
                $found[] = [
                    "cell" => $cell->row . $cell->col,
                    "method" => "One Choice",
                    "action" => "Places",
                    "values" => $cell->get_value()
                ];
            }
        }
        return $found;
    }

}
