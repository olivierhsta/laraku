<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException,Exception;

/**
 * Class to solve elimination problems.
 *
 * If a cell has a pencil mark that none of the other cells in one of its groups
 * has, we can assume that the value of the cell is the value of the said
 * pencil mark.
 */
class EliminationSolver extends Solver
{

    protected $grid;

    public function __construct(Grid $grid)
    {
        Solver::__construct($grid);
    }

    /**
     * Executes the elimination algorithm on the whole grid.
     *
     * For every cell, it checks if it has a pencil mark that no other cell in
     * one of its groups has.  If it is the only cell of the group to have it,
     * it must therefor be its value.
     */
    public function solve()
    {
        foreach ($this->grid->getGrid() as $cell)
        {
            $present = false;
            if ($cell->isEmpty()  && !empty($cell->getPencilMarks()))
            {
                $pencilMark = $this->eliminationBy($cell, 'row');
                if (!$pencilMark) // if we didn't find a value
                {
                    $pencilMark = $this->eliminationBy($cell, 'col');
                }
                if (!$pencilMark) // if we didn't find a value
                {
                    $pencilMark = $this->eliminationBy($cell, 'box');
                }

                if ($pencilMark)
                {
                    $cell->setValue($pencilMark);
                    $this->markMove($cell);
                }
            }
        }
    }

    /**
     * Executes the elimination algorithm only on the given groups.
     *
     * If the cell is the only one that can contain a given number in its
     * group, we affect it this number.
     *
     * @param  string $groupName group on which to practice the elimnation process.
     *                           throws InvalidArgumentException if the given string is
     *                           not one of 'col', 'row' or 'box'
     *
     * @return boolean|int new cell value if one of the cell's pencil mark was not found in the group, false otherwise
     */
    private function eliminationBy($cell, $groupName)
    {
        Solver::validateGroupName($groupName);

        foreach ($cell->getPencilMarks() as $pencilMark)
        {
            $present = false;
            $getter = 'get'.ucfirst($groupName);
            foreach ($this->grid->$getter($cell->$groupName()) as $otherCell)
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

    /**
     * Creates an entry in the $found array.
     *  The structure is
     *      "cell"   => [cell position],
     *      "method" => "Elimination",
     *      "values" => [placed number],
     *      "grid"   => [grid encoding]
     *
     * @param  Cell $cell        affected cell
     */
    public function markMove($cell, $pencilMarks=null, $args=array()) {
        $this->found[] = [
            "cell" => $cell->row() . $cell->col(),
            "method" => "Elimination",
            "values" => $cell->getValue(),
            "grid" => $this->grid->encoding(),
        ];
    }
}
