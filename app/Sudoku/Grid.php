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
        for ($i=1;$i<=9;$i++)
        {
            array_unshift($this->boxes[$i], "");
            unset($this->boxes[$i][0]);
            array_unshift($this->columns[$i], "");
            unset($this->column[$i][0]);
            array_unshift($this->rows[$i], "");
            unset($this->rows[$i][0]);
        }
    }

    public function getCell($index)
    {
        return $this->grid[$index];
    }

    public function getGrid()
    {
        return $this->grid;
    }

    public function getBoxes()
    {
        return $this->boxes;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getBox($index)
    {
        return $this->boxes[$index];
    }

    public function getRow($index)
    {
        return $this->rows[$index];
    }

    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    public static function getValues($aglo)
    {
        $values = array();
        foreach ($aglo as $cell)
        {
            $values[] = $cell->value;
        }
        return $values;
    }

    public function find_buddies(Cell $cell)
    {
        $buddies = array();

        foreach ($this->getRow($cell->row) as $row_cell)
        {
            if ($row_cell != $cell)
            {
                $buddies[] = $row_cell;
            }
        }

        foreach ($this->getColumn($cell->column) as $col_cell)
        {
            if ($col_cell != $cell && !in_array($col_cell, $buddies))
            {
                $buddies[] = $col_cell;
            }
        }

        foreach ($this->getBox($cell->box) as $box_cell)
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
