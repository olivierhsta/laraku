<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException,Exception;

class EliminationSolver extends Solver
{

    protected $grid;

    public function __construct(Grid $grid)
    {
        Solver::__construct($grid);
    }

    /**
     * Executes the elimination algorithm on the whole grid
     *
     * This calls the function elimination_by with the parameters 'row', 'col'
     * and 'box' in that order.
     */
    public function grid_solve()
    {
        $found_row = $this->eliminationBy('row');
        $found_col = $this->eliminationBy('col');
        $found_box = $this->eliminationBy('box');
        return array_merge($found_row, $found_col, $found_box);
    }

    public function group_solve(array $group)
    {

    }

    /**
     * Executes the elimination algorithm only on the given group
     *
     * If the cell is the only one that can contain a given number in its
     * group, we affect it this number.
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found[] = [
     *          "cell" => "13",
     *          "method" => "Elimination",
     *          "action" => "Contains",
     *          "values" => 8
     *      ];
     *
     * @param  string $group group on which to practice the elimnation process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     */
    private function eliminationBy($group_name)
    {
        Solver::validate_group_name($group_name);

        $found = array();

        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty()  && !empty($cell->get_pencil_marks()))
            {
                foreach ($cell->get_pencil_marks() as $pencil_mark)
                {
                    $present = false;
                    $getter = 'get_'.$group_name;
                    foreach ($this->grid->$getter($cell->$group_name) as $other_cell)
                    {
                        if ($other_cell->is_empty() && $other_cell != $cell)
                        {
                            if (in_array($pencil_mark, $other_cell->get_pencil_marks()))
                            {
                                $present = true;
                                break;
                            }
                        }
                    }
                }
                if (!$present)
                {
                    $cell->set_value($pencil_mark);
                    $found[] = [
                        "cell" => $cell->row . $cell->col,
                        "method" => "Elimination",
                        "action" => "Places",
                        "values" => $cell->get_value()
                    ];
                }
            }
        }
        return $found;
    }
}
