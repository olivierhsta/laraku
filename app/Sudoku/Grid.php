<?php

namespace App\Sudoku;

use InvalidArgumentException;

use App\Sudoku\Cell;

/**
 * Grid encoding
 *  __________________________________________
 * | 00  01  02  |  09  10  11  |  18  19  20 |
 * | 03  04  05  |  12  13  14  |  21  22  23 |
 * | 06  07  08  |  15  16  17  |  24  25  26 |
 *  ––––––––––––––––––––––––––––––––––––––––––
 * | 27  28  29  |  36  37  38  |  45  46  47 |
 * | 30  31  32  |  39  40  41  |  48  49  50 |
 * | 33  34  35  |  42  43  44  |  51  52  53 |
 *  ––––––––––––––––––––––––––––––––––––––––––
 * | 54  55  56  |  63  64  65  |  72  73  74 |
 * | 57  58  59  |  66  67  68  |  75  76  77 |
 * | 60  61  62  |  69  70  71  |  78  79  80 |
 *  ‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾
 */
class Grid
{

    private static $row_indexes = [
        [0,1,2,9,10,11,18,19,20],
        [3,4,5,12,13,14,21,22,23],
        [6,7,8,15,16,17,24,25,26],
        [27,28,29,36,37,38,45,46,47],
        [30,31,32,39,40,41,48,49,50],
        [33,34,35,42,43,44,51,52,53],
        [54,55,56,63,64,65,72,73,74],
        [57,58,59,66,67,68,75,76,77],
        [60,61,62,69,70,71,78,79,80]
    ];
    public $rows = array(array());
    public $columns = array(array());
    public $boxes = array(array());
    public $grid = array();

    public function __construct($grid)
    {
        for( $i=0 ; $i < strlen($grid) ; $i++ )
        {
            $cell_value = (int)$grid[$i];

            if ( (string)$cell_value != $grid[$i] )
            {
                throw new InvalidArgumentException("Grid must only contain integers");
            }

            $box = intdiv($i,9);
            $column = (($box)*3)%9 + $i%3;

            for ( $j=0 ; $j < sizeof(Grid::$row_indexes) ; $j++ )
            {
                $row_index = Grid::$row_indexes[$j];
                if (in_array($i, $row_index))
                {
                    $row = $j;
                    break;
                }
            }

            $cell = new Cell($this, $cell_value, $row+1, $column+1);
            $this->grid[$i] = $cell;

            $this->boxes[$box+1][] = $cell;
            $this->columns[$column+1][] = $cell;
            $this->rows[$row+1][] = $cell;
        }
    }

    public function get_cell($index)
    {
        return $this->grid[$index];
    }

    public function get_grid()
    {
        return $this->grid;
    }

    public function get_boxes()
    {
        return $this->boxes;
    }

    public function get_rows()
    {
        return $this->rows;
    }

    public function get_cols()
    {
        return $this->columns;
    }

    public function get_box($index)
    {
        return $this->boxes[$index];
    }

    public function get_row($index)
    {
        return $this->rows[$index];
    }

    public function get_col($index)
    {
        return $this->columns[$index];
    }

    public static function get_values($aglo)
    {
        $values = array();
        foreach ($aglo as $cell)
        {
            $values[] = $cell->get_value();
        }
        return $values;
    }

    public function find_buddies(Cell $cell)
    {
        $buddies = array();

        foreach ($this->get_row($cell->row) as $row_cell)
        {
            if ($row_cell != $cell)
            {
                $buddies[] = $row_cell;
            }
        }

        foreach ($this->get_col($cell->column) as $col_cell)
        {
            if ($col_cell != $cell && !in_array($col_cell, $buddies))
            {
                $buddies[] = $col_cell;
            }
        }

        foreach ($this->get_box($cell->box) as $box_cell)
        {
            if ($box_cell != $cell && !in_array($box_cell, $buddies))
            {
                $buddies[] = $box_cell;
            }
        }
        $buddies = array_filter($buddies); // remove empty values
        return $buddies;
    }
}
