<?php

namespace App\Sudoku;
use App\Sudoku\Grid;

class Solver
{

    protected $grid;
    private $found = array(array());
    public $full_aglo = [1,2,3,4,5,6,7,8,9];

    public function __construct($grid)
    {
        unset($this->found[0]);
        $this->grid = $grid;
    }

    /**
     * Entry point for the solving algorithm
     * @return Grid       Instante of Sudoku\Grid reprensenting the solved form of the sudoku (if solvable).
     */
    public function solve(){
        do {
            $this->write_pencil_marks();
            $onechoice = $this->onechoice();
            $this->write_pencil_marks();
            $scanning = $this->scanning();
        } while ($onechoice || $scanning);
        return $this->grid->get_rows();
    }

    public function write_pencil_marks()
    {
        $one_nine = [1,2,3,4,5,6,7,8,9];
        foreach ($this->grid->get_grid() as $cell)
        {
            $buddies = Grid::get_values($cell->get_buddies());
            $cell->set_pencil_marks(array_diff($one_nine, $buddies));
        }
    }

    public function onechoice()
    {
        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            $pencil_marks = $cell->get_pencil_marks();
            if ($cell->value == 0 && sizeof($pencil_marks) == 1)
            {
                $cell->set_value($pencil_marks[0]);
                $this->found["One Choice"][$cell->row . $cell->column] = $cell->get_value();
                $local_found = true;
            }
        }
        return $local_found;
    }

    public function scanning()
    {
        $local_found = false;
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->get_value() == 0)
            {
                foreach ($cell->get_pencil_marks() as $pencil_mark)
                {
                    $present = false;
                    foreach ($this->grid->get_row($cell->row) as $other_cell)
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
                        $this->found["Scanning"][$cell->row . $cell->column] = $cell->get_value();
                        $local_found = true;
                        break 2;
                    }
                    $present = false;
                    foreach ($this->grid->get_col($cell->column) as $other_cell)
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
                        $this->found["Scanning"][$cell->row . $cell->column] = $cell->get_value();
                        $local_found = true;
                        break 2;
                    }
                    $present = false;
                    foreach ($this->grid->get_box($cell->box) as $other_cell)
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
                        $this->found["Scanning"][$cell->row . $cell->column] = $cell->get_value();
                        $local_found = true;
                        break 2;
                    }
                }
            }
        }
        return $local_found;
    }

    public function get_found_values()
    {
        return $this->found;
    }

}
