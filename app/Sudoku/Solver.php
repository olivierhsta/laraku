<?php

namespace App\Sudoku;
use App\Sudoku\Grid;

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
        do {
            $onechoice = $this->onechoice();
            $elimination = $this->elimination();
            $naked_pair = $this->naked_pair();
        } while ($onechoice || $elimination || $naked_pair);

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
            $buddies = Grid::get_values($cell->get_buddies());
            $cell->set_pencil_marks(array_diff($one_nine, $buddies));
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
     *
     * @return boolean $local_found     true if we found any element by one choice, false otherwise
     */
    private function onechoice()
    {
        $local_found = false;
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
                $local_found = true;
            }
        }
        return $local_found;
    }

    /**
     * Executes the elimination algorithm on the whole grid
     *
     * This calls the function elimination_by with the parameters 'row', 'col'
     * and 'box' in that order.
     *
     * @return boolean $local_found  true if we found any element by one choice, false otherwise
     */
    private function elimination()
    {
        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->get_value() == 0)
            {
                foreach ($cell->get_pencil_marks() as $pencil_mark)
                {
                    if ($local_found = $this->elimination_by('row')) break;
                    if ($local_found = $this->elimination_by('col')) break;
                    if ($local_found = $this->elimination_by('box')) break;
                }
            }
        }
        return $local_found;
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
     * @return boolean $local_found  true if we found any element by one choice, false otherwise
     */
    private function elimination_by($agglo)
    {
        if ($agglo !== 'col' && $agglo !== 'row' && $agglo !== 'box')
        {
            throw new InvalidArgumentException("agglomeration must be either named 'row', 'col' or 'box', given name was : " . $agglo);
        }

        $present = false;
        $getter = 'get_'.$agglo;
        foreach ($this->grid->$getter($cell->$agglo) as $other_cell)
        {
            if ($other_cell->get_value() == 0 && $other_cell != $cell)
            {
                if (in_array($pencil_mark, $other_cell->get_pencil_marks()))
                {
                    $present = true;
                    break;
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
            break;
        }
        return !$present;
    }

    /**
     * Executes the naked pair algorithm on the whole grid
     *
     * This calls the function naked_pair_by with the parameters 'row', 'col'
     * and 'box' in that order.
     *
     * @return boolean $local_found     true if we found any element by one choice, false otherwise
     */
    private function naked_pair()
    {
        return
            $this->naked_pairs_by('row')
            || $this->naked_pairs_by('col')
            || $this->naked_pairs_by('box');
    }

    /**
     * Executes the naked pair algorithm only on the given agglomeration
     *
     * If a cell has only two pencil marks and another cell also only has those
     * two same pencil marks, these marks can be removed from every other cell
     * in the agglomeration
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found["Naked Pair"]["13"] = [
     *          "action" => "Remove Pencil Marks",
     *          "values" => 8
     *      ];
     *
     * @param  string $agglo agglomeration on which to practice the naked pair process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     * @return boolean $local_found true    if we found any element by one choice, false otherwise
     */
    private function naked_pair_by($agglo)
    {
        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            $cell_pm = $cell->get_pencil_marks();
            if (count($cell_pm) == 2)
            {
                $getter = 'get_'.$agglo;
                $$agglo = $this->grid->$getter($cell->$agglo);
                foreach ($$agglo as $compare_cell)
                {
                    $compare_cell_pm = $compare_cell->get_pencil_marks();
                    if ($compare_cell != $cell
                    && count($compare_cell_pm) == 2
                    && empty(array_diff($cell_pm, $compare_cell_pm)))
                    {
                        foreach($$agglo as $remove_cell)
                        {
                            if ($remove_cell != $cell && $remove_cell != $compare_cell && $remove_cell->is_empty())
                            {
                                $remove_cell->remove_pencil_marks($cell_pm);
                                $this->found["Naked Pair"][$remove_cell->row . $remove_cell->col] = [
                                    "action" => "Remove Pencil Marks",
                                    "values" => $cell_pm
                                ];
                            }
                        }
                        $local_found = true;
                    }

                }
            }
        }
        return $local_found;
    }




}
