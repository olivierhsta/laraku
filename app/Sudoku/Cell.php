<?php

namespace App\Sudoku;

class Cell
{

    public $grid;
    public $value;
    public $row;
    public $column;
    public $box;
    private $buddies;
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

    public function __construct($grid, $value, $row, $column)
    {
        $this->grid = $grid;
        $this->value = $value;
        $this->row = $row;
        $this->column = $column;

        for ($i = 1 ; $i <= 3; $i++)
        {
            if ($row <= 3*$i)
            {
                for ($j = 1 ; $j <= 3; $j++)
                {
                    if ($column <= 3*$j)
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

    public function getBuddies()
    {
        if (empty($this->buddies))
        {
            $this->buddies = $this->grid->find_buddies($this);
        }
        return $this->buddies;
    }

    public function setPencilMarks($values)
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
        $this->pencil_marks = [0,0,0,0,0,0,0,0,0];
    }

    public function getPencilMarks()
    {
        return $this->pencil_marks;
    }

}
