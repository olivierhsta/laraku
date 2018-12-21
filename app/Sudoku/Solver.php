<?php

namespace App\Sudoku;
use App\Sudoku\Grid;
use InvalidArgumentException;
use Exception;

class Solver
{

    protected $grid;
    private $found = array(array());
    public $full_agglo = [1,2,3,4,5,6,7,8,9];

    public function __construct($grid)
    {
        unset($this->found[0]);
        $this->grid = $grid;
    }

    /**
     * Entry point for the solving algorithm
     * @return Grid       Instante of Sudoku\Grid reprensenting the solved form of the sudoku (if solvable).
     */
    public function solve()
    {
        $this->write_pencil_marks();
        $limit_counter = 0;
        do {
            $before_grid = clone $this->grid;
            $before_found = $this->found;
            $this->onechoice();
            $this->elimination();
            $this->naked_subset();
            $limit_counter++;
        } while (sizeof($before_found, $mode=COUNT_RECURSIVE) != sizeof($this->found,$mode=COUNT_RECURSIVE)
                 && $limit_counter < 100);
        return $this->grid->get_rows();
    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return null
     */
    public function write_pencil_marks()
    {
        $one_nine = [1,2,3,4,5,6,7,8,9];
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty())
            {
                $buddies = Grid::get_values($cell->get_buddies());
                $cell->set_pencil_marks(array_diff($one_nine, $buddies));
            }
        }
    }

    public function get_found_values()
    {
        return $this->found;
    }

    /**
     * Executes the one choice algorithm one the whole grid
     *
     * If a cell only as one pencil mark (aka one possible number can fill it),
     * the value is set to the cell
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found["One Choice"]["13"] = [
     *          "action" => "Contains",
     *          "values" => 8
     *      ];
     */
    private function onechoice()
    {
        foreach ($this->grid->get_grid() as $cell)
        {
            $pencil_marks = $cell->get_pencil_marks();
            if ($cell->is_empty() && sizeof($pencil_marks) == 1)
            {
                $cell->set_value($pencil_marks[0]);
                $this->found["One Choice"][$cell->row . $cell->col] = [
                    "action" => "Contains",
                    "values" => $cell->get_value()
                ];
            }
        }
    }

    /**
     * Executes the elimination algorithm on the whole grid
     *
     * This calls the function elimination_by with the parameters 'row', 'col'
     * and 'box' in that order.
     */
    private function elimination()
    {
        $this->elimination_by('row');
        $this->elimination_by('col');
        $this->elimination_by('box');
    }

    /**
     * Executes the elimination algorithm only on the given agglomeration
     *
     * If the cell is the only one that can contain a given number in its
     * agglomeration, we affect it this number.
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found["Elimination"]["13"] = [
     *          "action" => "Contains",
     *          "values" => 8
     *      ];
     *
     * @param  string $agglo agglomeration on which to practice the elimnation process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     */
    private function elimination_by($agglo_name)
    {
        Solver::validate_agglo_name($agglo_name);

        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty()  && !empty($cell->get_pencil_marks()))
            {
                foreach ($cell->get_pencil_marks() as $pencil_mark)
                {
                    $present = false;
                    $getter = 'get_'.$agglo_name;
                    foreach ($this->grid->$getter($cell->$agglo_name) as $other_cell)
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
                    $this->found["Elimination"][$cell->row . $cell->col] = [
                        "action" => "Contains",
                        "values" => $cell->get_value()
                    ];
                }
            }
        }
    }

    /**
     * Executes the naked subset algorithm on the whole grid
     *
     * This calls the function naked_subset_by with the parameters 'row', 'col'
     * and 'box' in that order.
     */
    private function naked_subset()
    {
        $this->naked_subset_by('row');
        $this->naked_subset_by('col');
        $this->naked_subset_by('box');
    }

    /**
     * Executes the naked subset algorithm only on the given agglomeration
     *
     * If a cell has only two pencil marks and another cell also only has those
     * two same pencil marks, these marks can be removed from every other cell
     * in the agglomeration.  This is a naked pair.  This can be generalized
     * to triplet, quadruplet and quinduplet.
     *
     * @param  string $agglo agglomeration on which to practice the naked subset process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     */
    private function naked_subset_by($agglo_name)
    {
        Solver::validate_agglo_name($agglo_name);

        $getter = 'get_'.$agglo_name.($agglo_name == 'box' ? 'es' : 's');
            foreach ($this->grid->$getter() as $agglo)
            {
                for ($i=2; $i <= 5; $i++)
                {
                    $before_found = $this->found;
                    $this->naked_subset_recursion($agglo, $i);
                    if (sizeof($before_found, $mode=COUNT_RECURSIVE) != sizeof($this->found,$mode=COUNT_RECURSIVE)) return null;
                }
            }
    }

    /**
     * Recursive function that allows to find naked subset of any size.
     *
     * For every cell that we removed pencil marks, we add an element to the $found array
     * ex.  $this->found["Naked Pair"]["13"] = [
     *          "action" => "Remove Pencil Marks",
     *          "values" => [1,5]
     *      ];
     *
     * @param array[Cell] $agglo   Agglomeration on which to perform the algorithm.
     *                             Should be either a box, a row or a column
     * @param int $depth   Size of the naked subset (2 = naked pair, 3 = naked triple, etc.)
     * @param array  $indexes Internal parameter that should not be set at first call.
     *                        It holds the indexes of the subset's cells that we
     *                        are currently testing.
     */
    private function naked_subset_recursion($agglo, $depth, $indexes = [])
    {
        if ($depth == 0)
        {
            $subset = array();
            foreach ($indexes as $index)
            {
                if ($agglo[$index]->is_empty())
                {
                    $subset[] = $agglo[$index];
                }
            }
            if (!empty($subset) && $pm_to_remove = $this->is_naked_subset($subset))
            {
                foreach ($agglo as $cell)
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
                            $this->found["Naked ". $subset_type][$cell->row . $cell->col] = [
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
                $this->naked_subset_recursion($agglo, $depth-1, $tmp_indexes);
            }
        }
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
     * @param array[Cell] $subset   Subset for which to test its nakedness.
     *                              Only accepted subset size are between 2 and 5
     *                              (subset bigger than 5 are not realistically possible in sudokus)
     * @return boolean|array[int] false if the subset is not naked and the shared pencil marks if it is
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

    /**
     * Helper function to check if the agglomeration name given is valid.
     * Throws InvalidArgumentException if not.
     *
     * Valid names are 'box', 'row', 'col'
     *
     * @param  string $agglo_name Agglomeration name to test
     */
    public static function validate_agglo_name($agglo_name)
    {
        if ($agglo_name !== 'col' && $agglo_name !== 'row' && $agglo_name !== 'box')
        {
            throw new InvalidArgumentException("Agglomeration must be either named 'row', 'col' or 'box', given name was : " . $agglo_name);
        }
    }




}
