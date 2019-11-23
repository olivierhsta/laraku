<?php

namespace App\Sudoku\Solvers;

use App\Sudoku\Grid;

/**
 * Class to solve with Interactions.
 *
 * Interactions occurs when one given non-unique pencil mark value in a group
 * happens to be only in one other group.  When this happens, all the
 * pencil marks with the same value in the second group can be erased.
 *
 * For example, if we have a box where all the pencil marks '1' are in the
 * same row, then every other pencil marks '1' in that row can be erased.
 */
class InteractionSolver extends Solver
{
    public function __construct(Grid &$grid) {
        Solver::__construct($grid);
    }

    /**
     * Executes the interaction algorithm on the whole grid.
     *
     * It goes first over every box of the grid and for each box,
     * it checks if there is any pencil mark that is only in any
     * one row/column.  If there is suck pencil mark, remove it from
     * all other cell in the row/column.
     *
     * It then performs the same logic with the rows, and then the columns.
     * The only difference is that for rows and columns, there is only
     * one other group type that needs to be check (boxes) instead of two.
     */
    public function solve() {
        $this->groupSolve('box','row');
        $this->groupSolve('box','col');
        $this->groupSolve('row','box');
        $this->groupSolve('col','box');
    }

    /**
     * Performs the Interaction algorithm on any combinasion of group type.
     *
     * For references, group types combination for Interaction are
     *      - box, row
     *      - box, col
     *      - row, box
     *      - col, box
     *
     * @param  string $scannedGroupName  group type that will be looped through.
     *                                   Can only be one of 'row','col','box'
     * @param  string $comparedGroupName group type in which Interaction will be looked for.
     *                                   Can only be one of 'row','col','box'
     */
    private function groupSolve($scannedGroupName, $comparedGroupName) {
        self::validateGroupName($scannedGroupName);
        self::validateGroupName($comparedGroupName);

        $scannedGetter = 'get'.ucfirst($scannedGroupName).($scannedGroupName == 'box' ? 'es' : 's');
        $comparedGetter = 'get'.ucfirst($comparedGroupName).($comparedGroupName == 'box' ? 'es' : 's');


        foreach ($this->grid->$scannedGetter() as $groupIndex => $group)
        {
            for ($pencilMark=1; $pencilMark <= 9; $pencilMark++)
            {
                if ($otherGroup = $this->pencilMarksInGroupAreInOnlyOneOtherGroup($pencilMark, $group, $comparedGroupName))
                {
                    foreach ($otherGroup as $cell)
                    {
                        if ($cell->isEmpty() && $cell->$scannedGroupName() != $groupIndex)
                        {
                            $cell->removePencilMarks($pencilMark);
                            $this->markMove($cell, $pencilMark);
                        }
                    }
                }
            }
        }
    }


    /**
     * Checks if the given pencil mark value is in only one group
     * other then the one that has been passed.
     *
     * For instance, if the parameters are
     *      - $pencilMark = 1
     *      - $group = [any given box],
     *      - $otherGroupName = 'row'
     * It will check if all the 1s in the box are placed on the same row.
     *
     * @param  int         $pencilMark     pencilMark value to look for
     * @param  array[cell] $group          group in which to look
     * @param  string      $otherGroupName group type in which to look for Interaction.
     *                                     Must be on of 'row','box','col'
     * @return array[cell]|boolean         false if the pencil mark of the given
     *                                     given value are not in the same group.
     *                                     Otherwise, the group in which they were
     *                                     found to be.
     */
    private function pencilMarksInGroupAreInOnlyOneOtherGroup($pencilMark, $group, $otherGroupName) {
        self::validateGroupName($otherGroupName);

        $pencilMarksGroups = array();
        foreach ($group as $cell)
        {
            if($cell->isEmpty() && in_array($pencilMark, $cell->getPencilMarks()))
            {
                $pencilMarksGroups[] = $cell->$otherGroupName();
            }
        }
        if (count(array_unique($pencilMarksGroups)) === 1)
        {
            $getter = 'get'.ucfirst($otherGroupName);
            return $this->grid->$getter($pencilMarksGroups[0]);
        }
        return false;
    }

    /**
     * Creates an entry in the $found array.
     *  The structure is
     *      "cell"   => [cell position],
     *      "method" => "Interaction",
     *      "values" => [pencil mark value removed],
     *      "grid"   => [grid encoding]
     *
     * @param  Cell $cell        affected cell
     * @param  int  $pencilMark  pencil mark value
     */
    private function markMove($cell, $pencilMark) {
        $this->found[] = [
            "cell" => $cell->row() . $cell->col(),
            "method" => "Interaction",
            "values" => array($pencilMark),
            "grid" => $this->grid->encoding(),
        ];
    }
}
