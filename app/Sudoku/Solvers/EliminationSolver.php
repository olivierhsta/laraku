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
    public function gridSolve()
    {
        return $this->groupSolve($this->grid->getGrid());
    }

    public function groupSolve(array $group)
    {
        $found = array();

        foreach ($group as $cell)
        {
            $present = false;
            if ($cell->isEmpty()  && !empty($cell->getPencilMarks()))
            {
                $pencilMark = $this->eliminationBy($cell, 'row');
                if (!$pencilMark)
                {
                    $pencilMark = $this->eliminationBy($cell, 'col');
                }
                if (!$pencilMark)
                {
                    $pencilMark = $this->eliminationBy($cell, 'box');
                }

                if ($pencilMark)
                {
                    $cell->setValue($pencilMark);
                    $found[] = [
                        "cell" => $cell->row . $cell->col,
                        "method" => "Elimination",
                        "action" => "Places",
                        "values" => $cell->getValue()
                    ];
                }
            }
        }
        return $found;
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
     * @param  string $groupName group on which to practice the elimnation process.
     *                           throws InvalidArgumentException if the given string is
     *                           not one of 'col', 'row' or 'box'
     *
     * @return boolean|int new cell value if one of the cell's pencil mark was found in the group, false otherwise
     */
    private function eliminationBy($cell, $groupName)
    {
        Solver::validateGroupName($groupName);
        foreach ($cell->getPencilMarks() as $pencilMark)
        {
            $present = false;
            $getter = 'get'.ucfirst($groupName);
            foreach ($this->grid->$getter($cell->$groupName) as $otherCell)
            {
                if ($otherCell->isEmpty() && $otherCell != $cell)
                {
                    if (in_array($pencilMark, $otherCell->getPencilMarks()))
                    {
                        $present = true;
                    }
                }
            }
            if (!$present)
            {
                return $pencilMark;
            }
        }
        return false;
    }
}
