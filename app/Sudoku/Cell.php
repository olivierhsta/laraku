<?php

namespace App\Sudoku;

use InvalidArgumentException;
use Exception;

class Cell
{

    private $grid;
    private $value;
    public $row;
    public $col;
    public $box;
    private $buddies;

    /**
     * Pencil marks flags.  Each value (1 to 9) is either 0 or 1.
     * 1 meaning that the pencil marks is set for the cell
     * @var array[int=>int]
     */
    public $pencil_marks = [
        1=>0,
        2=>0,
        3=>0,
        4=>0,
        5=>0,
        6=>0,
        7=>0,
        8=>0,
        9=>0
    ];

    public function __construct($grid, $value, $row, $col)
    {
        $this->grid = $grid;
        $this->value = $value;
        $this->row = $row;
        $this->col = $col;

        for ($i = 1 ; $i <= 3; $i++)
        {
            if ($row <= 3*$i)
            {
                for ($j = 1 ; $j <= 3; $j++)
                {
                    if ($col <= 3*$j)
                    {
                        switch ($i . $j)
                        {
                            case "11" : $this->box = 1;
                                break 3;
                            case "12" : $this->box = 2;
                                break 3;
                            case "13" : $this->box = 3;
                                break 3;
                            case "21" : $this->box = 4;
                                break 3;
                            case "22" : $this->box = 5;
                                break 3;
                            case "23" : $this->box = 6;
                                break 3;
                            case "31" : $this->box = 7;
                                break 3;
                            case "32" : $this->box = 8;
                                break 3;
                            case "33" : $this->box = 9;
                                break 3;
                        }
                    }
                }
            }
        }
    }

    public function get_buddies()
    {
        if (empty($this->buddies))
        {
            $this->buddies = $this->grid->find_buddies($this);
        }
        return $this->buddies;
    }

    /**
     * Set the pencil marks to the given values.
     *
     * Throws InvalidArgumentException if the given values aren't between 1
     *
     * @param array[int] $values    array of pencil marks between 1 and 9
     *                              eg. [1,2,6]
     */
    public function set_pencil_marks($values)
    {
        $one_nine = [1,2,3,4,5,6,7,8,9];
        if (!empty(array_diff($values, $one_nine)))
        {
            throw new InvalidArgumentException("Pencil marks must be of value 1 to 9");
        }
        $this->reset_pencil_marks();
        foreach ($values as $index)
        {
            $this->pencil_marks[$index] = 1;
        }
    }

    private function reset_pencil_marks()
    {
        $this->pencil_marks = [
            1=>0,
            2=>0,
            3=>0,
            4=>0,
            5=>0,
            6=>0,
            7=>0,
            8=>0,
            9=>0
        ];
    }

    public function get_pencil_marks()
    {
        if ($this->value != 0)
        {
            return array();
        }
        $marks = array();
        foreach ($this->pencil_marks as $value => $mark)
        {
            if ($mark == 1)
            {
                $marks[] = $value;
            }
        }
        return $marks;
    }

    public function remove_pencil_marks($marks = null)
    {
        $removed_pm = array();
        if (is_int($marks) && $marks <= 9 && $marks >= 1)
        {
            if ($this->pencil_marks[$marks] == 1)
            {
                $removed_pm[] = $marks;
            }
            $this->pencil_marks[$marks] = 0;
        }
        else if (is_array($marks))
        {
            foreach ($marks as $mark)
            {
                if (!is_int($mark) || $mark > 9 || $mark < 1)
                {
                    throw new InvalidArgumentException('Value must be an integer between 1 and 9.  Value given : ' . $mark);
                }
                if ($this->pencil_marks[$mark] == 1)
                {
                    $removed_pm[] = $mark;
                }
                $this->pencil_marks[$mark] = 0;
            }
        }
        else if (is_null($marks))
        {
            $removed_pm = $this->get_pencil_marks();
            $this->reset_pencil_marks();
        }
        else
        {
            throw new InvalidArgumentException('Value must be an integer between 1 and 9.  Value given : ' . $mark);
        }
        return $removed_pm;
    }

    /**
     * Sets the value of the cell if it wasn't already set.
     * It also removes the value number from the pencil marks
     * of every buddies of this cell
     *
     * @param int $value the given value
     * @return int $value   new value of the cell
     */
    public function set_value($value)
    {
        if ($this->value == 0)
        {
            $this->value = $value;
            foreach ($this->get_buddies() as $buddy)
            {
                $buddy->remove_pencil_marks($this->get_value());
            }
        }
        return $this->value;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function is_empty()
    {
        return $this->value == 0;
    }

}
