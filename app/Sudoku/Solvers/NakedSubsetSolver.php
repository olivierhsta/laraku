<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

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
     * This calls the function naked_subset_by with the parameters 'row', 'col'
     * and 'box' in that order.
     */
    public function grid_solve()
    {
        $found_row = $this->naked_subset_by('row');
        // if ($return) return $this->found;
        $found_col = $this->naked_subset_by('col');
        // if ($return) return $this->found;
        $found_box = $this->naked_subset_by('box');
        return array_merge($found_row, $found_col, $found_box);
    }

    public function group_solve(array $group)
    {

    }

    /**
     * Executes the naked subset algorithm only on the given group
     *
     * If a cell has only two pencil marks and another cell also only has those
     * two same pencil marks, these marks can be removed from every other cell
     * in the group.  This is a naked pair.  This can be generalized
     * to triplet, quadruplet and quinduplet.
     *
     * @param  string $group group on which to practice the naked subset process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     */
    private function naked_subset_by($group_name)
    {
        Solver::validate_group_name($group_name);

        $found = array();

        $getter = 'get_'.$group_name.($group_name == 'box' ? 'es' : 's');
        for ($i=2; $i <= 5; $i++)
        {
            foreach ($this->grid->$getter() as $group)
            {
                    $before_found = $found;
                    $found = array_merge($found, $this->naked_subset_recursion($group, $i));
                    // if (sizeof($before_found) != sizeof($found)) return $found;
            }
        }
        return $found;
    }

    /**
     * Recursive function that allows to find naked subset of any size.
     *
     * For every cell that we removed pencil marks, we add an element to the $found array
     *
     * @param Cell[] $group   group on which to perform the algorithm.
     *                             Should be either a box, a row or a column
     * @param int $depth   Size of the naked subset (2 = naked pair, 3 = naked triple, etc.)
     * @param int[]  $indexes Internal parameter that should not be set at first call.
     *                        It holds the indexes of the subset's cells that we
     *                        are currently testing.
     *
     * @return array[array[]] $found Found values.
     *              ex.    $found[] = [
     *                          "cell" => "13",
     *                          "method" => "Naked Pair",
     *                          "action" => "Remove Pencil Marks",
     *                          "values" => [1,5]
     *                      ];
     */
    private function naked_subset_recursion($group, $depth, $indexes = [])
    {
        $found = array();
        if ($depth == 0)
        {
            $subset = array();
            foreach ($indexes as $index)
            {
                if ($group[$index]->is_empty())
                {
                    $subset[] = $group[$index];
                }
            }
            if (!empty($subset) && $pm_to_remove = $this->is_naked_subset($subset))
            {
                foreach ($group as $cell)
                {
                    if ($cell->is_empty() && !in_array($cell,$subset))
                    {
                        $removed_pm = $cell->remove_pencil_marks($pm_to_remove);
                        if (!empty($removed_pm))
                        {
                            switch(sizeof($subset))
                            {
                                case 2: $subset_type = "Pair"; break;
                                case 3: $subset_type = "Triple"; break;
                                case 4: $subset_type = "Quadruplet"; break;
                                case 5: $subset_type = "Quintuplet"; break;
                                default: $subset_type = "Subset";
                            }
                            $found[] = [
                                "cell" => $cell->row . $cell->col,
                                "method" => "Naked ". $subset_type,
                                "action" => "Remove Pencil Marks",
                                "values" => $removed_pm
                            ];
                        }
                    }
                }
            }
        }
        else
        {
            $last_index = 0;
            foreach ($indexes as $index)
            {
                $last_index = $index;
            }
            for ($i = $last_index+1; $i <= 9-($depth-1) ; $i++)
            {
                $tmp_indexes = $indexes;
                $tmp_indexes[] = $i;
                $found = array_merge($found, $this->naked_subset_recursion($group, $depth-1, $tmp_indexes));
            }
        }
        return $found;
    }

    /**
     * Checks if the given subset is a naked subset.  This means it contains the
     * same number of pencil marks as its size.
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
    private function is_naked_subset($subset)
    {
        // subset bigger than 5 are not realistically possible in sudokus
        if (sizeof($subset) > 5 || sizeof($subset) < 2) return false;
        $pencil_marks = array();
        foreach ($subset as $cell)
        {
            $pencil_marks[] = $cell->get_pencil_marks();
        }

        // https://secure.php.net/manual/en/functions.arguments.php#functions.variable-arg-list.new
        $shared_pm = array_unique(array_merge(...$pencil_marks), SORT_REGULAR);
        if (sizeof($shared_pm) == sizeof($subset))
        {
            return $shared_pm;
        }
        return false;
    }
}
