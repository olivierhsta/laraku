<?php

namespace App\Sudoku;

use Illuminate\Database\Eloquent\Model;
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
class Grid extends Model
{

    protected $row_indexes = [
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
    protected $rows = array(array());
    protected $columns = array(array());
    protected $boxes = array(array());
    protected $grid = array();

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
            $column = ($box)*3 + $i%3;

            for ( $j=0 ; $j < sizeof($this->row_indexes) ; $j++ )
            {
                $row_index = $this->row_indexes[$j];
                if (in_array($i, $row_index))
                {
                    $row = $j;
                    break;
                }
            }

            $cell = new Cell($cell_value, $row+1, $column+1);
            $this->grid[$i] = $cell;

            $this->boxes[$box][] = $cell;
            $this->columns[$column][] = $cell;
            $this->rows[$row][] = $cell;
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
}
