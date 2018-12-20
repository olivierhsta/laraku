<?php

namespace App\Sudoku;
use App\Sudoku\Grid;
use InvalidArgumentException;

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
            $onechoice = $this->onechoice();
            $elimination = $this->elimination();
            $naked_subset = $this->naked_subset();
            $limit_counter++;
        } while (($onechoice || $elimination || $naked_subset) && $limit_counter < 1000);
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
        $local_found = $this->elimination_by('row');
        $local_found = $this->elimination_by('col') || $local_found;
        $local_found = $this->elimination_by('box') || $local_found;

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
            throw new InvalidArgumentException("Agglomeration must be either named 'row', 'col' or 'box', given name was : " . $agglo);
        }
        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty())
            {
                foreach ($cell->get_pencil_marks() as $pencil_mark)
                {
                    $present = false;
                    $getter = 'get_'.$agglo;
                    foreach ($this->grid->$getter($cell->$agglo) as $other_cell)
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
                    $local_found = true;
                }
            }
        }

        return $local_found;
    }

    /**
     * Executes the naked subset algorithm on the whole grid
     *
     * This calls the function naked_subset_by with the parameters 'row', 'col'
     * and 'box' in that order.
     *
     * @return boolean $local_found     true if we found any element by one choice, false otherwise
     */
    private function naked_subset()
    {
        return $this->naked_subset_by('row')
            || $this->naked_subset_by('col')
            || $this->naked_subset_by('box');
    }

    /**
     * Executes the naked subset algorithm only on the given agglomeration
     *
     * If a cell has only two pencil marks and another cell also only has those
     * two same pencil marks, these marks can be removed from every other cell
     * in the agglomeration.  This is a naked pair.  This can be generalized
     * to triplet, quadruplet and quinduplet.
     *
     * For every value found, we add an element to the $found array
     * ex.  $this->found["Naked Pair"]["13"] = [
     *          "action" => "Remove Pencil Marks",
     *          "values" => [1,5]
     *      ];
     *
     * @param  string $agglo agglomeration on which to practice the naked subset process.
     *                      throws InvalidArgumentException if the given string is
     *                      not one of 'col', 'row' or 'box'
     * @return boolean $local_found true    if we found any element by one choice, false otherwise
     */
    private function naked_subset_by($agglo, $verbose = false)
    {
        if ($agglo !== 'col' && $agglo !== 'row' && $agglo !== 'box')
        {
            throw new InvalidArgumentException("Agglomeration must be either named 'row', 'col' or 'box', given name was : " . $agglo);

        }

        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty())
            {
                $cell_pm = $cell->get_pencil_marks();
                $getter = 'get_'.$agglo;
                $$agglo = $this->grid->$getter($cell->$agglo);
                $cell_in_subset = array($cell);

                foreach ($$agglo as $compare_cell)
                {
                    $compare_cell_pm = $compare_cell->get_pencil_marks();
                    if ($compare_cell->is_empty()
                    && $compare_cell != $cell
                    && sizeof($compare_cell_pm) == sizeof($cell_pm)
                    && empty(array_diff($cell_pm, $compare_cell_pm)))
                    {
                        $cell_in_subset[] = $compare_cell;
                    }
                }

                if (sizeof($cell_pm) > 1 && sizeof($cell_in_subset) == sizeof($cell_pm))
                {
                    foreach($$agglo as $remove_cell)
                    {
                        if (!in_array($remove_cell, $cell_in_subset)
                            && $remove_cell->is_empty()
                            && sizeof(array_intersect($cell_pm, $remove_cell->get_pencil_marks())) > 0)
                        {
                            $removed_pm = $remove_cell->remove_pencil_marks($cell_pm);
                            switch(sizeof($cell_in_subset))
                            {
                                case 2: $subset_type = "Pair"; break;
                                case 3: $subset_type = "Triple"; break;
                                case 4: $subset_type = "Quadruplet"; break;
                                case 5: $subset_type = "Quintuplet"; break;
                                default: $subset_type = "";
                            }
                            $this->found["Naked ". $subset_type][$remove_cell->row . $remove_cell->col] = [
                                "action" => "Remove Pencil Marks",
                                "values" => $removed_pm
                            ];
                            $local_found = true;
                        }
                    }
                }
            }
        }
        return $local_found;
    }




}
