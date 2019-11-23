<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use App\Sudoku\Sudoku;
use InvalidArgumentException, Exception;

/**
 * Class to solve naked subsets.
 *
 * If a cell has only two pencil marks and another cell also only has those
 * two same pencil marks, these marks can be removed from every other cell
 * in the group.  This is a naked pair.  This can be generalized
 * to triplet, quadruplet and quinduplet.
 *
 */
class NakedSubsetSolver extends Solver
{

    protected $grid;

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
    }

    /**
     * Executes the naked subset algorithm on the whole grid
     *
     * It goes first over all the rows and, for each row, checks if the
     * pair of cells [(1,2), ..., (4,5), ..., (8,9)] form a naked pair.
     * It then does the same for every triplets to see if it form a naked triple
     * and then the quadruplet and quintuplet.
     *
     * It then does the same for every column and box (in that order)
     */
    public function solve()
    {
        $this->nakedSubsetBy('row');
        $this->nakedSubsetBy('col');
        $this->nakedSubsetBy('box');
    }

    /**
     * Executes the naked subset algorithm on the given group.
     *
     * It checks if any of the subset of the group is
     * a naked subset (from pair to quinduplets).
     *
     * @param  array  $group group on which to perform the algorithm
     */
    public function groupSolve(array $group)
    {
        for ($i=2; $i <= 5; $i++)
        {
            $this->nakedSubsetRecursion($group, $i);
        }
    }

    /**
     * Executes the naked subset algorithm only on
     * one set of group (rows, boxes or columns)
     *
     * @param  string $group group on which to practice the naked subset process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     */
    private function nakedSubsetBy($groupName)
    {
        Solver::validateGroupName($groupName);

        $getter = 'get'.ucfirst($groupName).($groupName == 'box' ? 'es' : 's');
        // uses of variable-variable. becomes either
        // $this->grid->getCols(), getRows() or getBoxes()
        foreach ($this->grid->$getter() as $group)
        {
            $this->groupSolve($group);
        }
    }

    /**
     * Recursive function that allows to find naked subset of any size.
     *
     * @param Cell[] $group   group on which to perform the algorithm.
     *                        Should be either a box, a row or a column.
     * @param int    $depth   Size of the naked subset (2 = naked pair, 3 = naked triple, etc.)
     * @param int[]  $indexes Internal parameter that should not be set at first call.
     *                        It holds the indexes of the subset's cells that we
     *                        are currently testing.
     */
    private function nakedSubsetRecursion($group, $depth, $indexes = [])
    {
        if ($depth == 0)
        {
            /*
             recursion tree leaf
             */
            $subset = array();
            foreach ($indexes as $index)
            {
                if ($group[$index]->isEmpty())
                {
                    $subset[] = $group[$index];
                }
            }
            if (!empty($subset) && $pmToRemove = $this->isNakedSubset($subset))
            {
                foreach ($group as $cell)
                {
                    if ($cell->isEmpty() && !in_array($cell,$subset))
                    {
                        $removedPM = $cell->removePencilMarks($pmToRemove);
                        if (!empty($removedPM))
                        {
                            $this->markMove($cell, $removedPM, $subset);
                        }
                    }
                }
            }
        }
        else
        {
            $lastIndex = 0;
            foreach ($indexes as $index)
            {
                $lastIndex = $index;
            }
            for ($i = $lastIndex+1; $i <= config()->get('sudoku.groupSize')-($depth-1) ; $i++)
            {
                $tmpIndexes = $indexes;
                $tmpIndexes[] = $i;
                $this->nakedSubsetRecursion($group, $depth-1, $tmpIndexes);
            }
        }
    }

    /**
     * Checks if the given subset is a naked subset.
     * If it is, it contains the same number of pencil marks as its size.
     *
     * eg. - subset of 3 cells with pencil marks [1,2]; [1,3]; [2,3] is a naked subset
     *       ( because sizeof([1,2,3]) == 3 )
     *     - subset of 3 cells with pencil marks [1,4]; [1,3]; [2,3] is not a naked subset
     *       ( because sizeof([1,2,3,4]) != 3 )
     *
     * @param Cell[] $subset   Subset for which to test its nakedness.
     *                              Only accepted subset size are between 2 and 5
     *                              (subset bigger than 5 are not realistically possible in sudokus)
     * @return boolean|int[] false if the subset is not naked and the shared pencil marks if it is
     */
    private function isNakedSubset($subset)
    {
        // subset bigger than 5 are not realistically possible in sudokus
        if (sizeof($subset) > 5 || sizeof($subset) < 2) return false;
        $pencilMarks = array();
        foreach ($subset as $cell)
        {
            $pencilMarks[] = $cell->getPencilMarks();
        }

        // source :
        // https://secure.php.net/manual/en/functions.arguments.php#functions.variable-arg-list.new
        $sharedPM = array_unique(array_merge(...$pencilMarks), SORT_REGULAR);
        if (sizeof($sharedPM) == sizeof($subset))
        {
            return $sharedPM;
        }
        return false;
    }

    /**
     * Creates an entry in the $found array.
     *  The structure is
     *      "cell"   => [cell position],
     *      "method" => "Naked [Pair|Triple|Quadruplet|Quintuplet|Subset]",
     *      "values" => [pencil mark values removed],
     *      "grid"   => [grid encoding]
     *
     * @param  Cell $cell        affected cell
     * @param  int  $pencilMark  pencil mark value
     */
    private function markMove($cell, $pencilMarks, $subset) {
        switch(count($subset))
        {
            case 2: $subsetType = "Pair"; break;
            case 3: $subsetType = "Triple"; break;
            case 4: $subsetType = "Quadruplet"; break;
            case 5: $subsetType = "Quintuplet"; break;
            default: $subsetType = "Subset";
        }
        $this->found[] = [
            "cell" => $cell->row() . $cell->col(),
            "method" => "Naked ". $subsetType,
            "values" => $pencilMarks,
            "grid" => $this->grid->encoding(),
        ];
    }
}
