<?php

namespace App\Sudoku;

use Illuminate\Database\Eloquent\Model;

class Cell extends Model
{

    public $value;
    public $row;
    public $column;
    public $box;

    public function __construct($value, $row, $column)
    {
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
                            case "12" : $this->box = 2;
                            case "13" : $this->box = 3;
                            case "14" : $this->box = 4;
                            case "15" : $this->box = 5;
                            case "16" : $this->box = 6;
                            case "17" : $this->box = 7;
                            case "18" : $this->box = 8;
                            case "19" : $this->box = 9;
                        }
                    }
                }
            }
        }

    }

}
