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

    private static $rowIndexes = [
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
    private $rows = array(array());
    private $columns = array(array());
    private $boxes = array(array());
    private $grid = array();

    public function __construct($grid)
    {
        for( $i=0 ; $i < strlen($grid) ; $i++ )
        {
            $cellValue = (int)$grid[$i];

            if ( (string)$cellValue != $grid[$i] )
            {
                throw new InvalidArgumentException("Grid must only contain integers");
            }

            $box = intdiv($i,9);
            $column = (($box)*3)%9 + $i%3;

            for ( $j=0 ; $j < sizeof(Grid::$rowIndexes) ; $j++ )
            {
                $rowIndex = Grid::$rowIndexes[$j];
                if (in_array($i, $rowIndex))
                {
                    $row = $j;
                    break;
                }
            }

            $cell = new Cell($this, $cellValue, $row+1, $column+1);
            $this->grid[$i] = $cell;

            $this->boxes[$box+1][0] = 0;
            $this->columns[$column+1][0] = 0;
            $this->rows[$row+1][0] = 0;

            $this->boxes[$box+1][] = $cell;
            $this->columns[$column+1][] = $cell;
            $this->rows[$row+1][] = $cell;

            // to start at natural sudoku index 1
            unset($this->boxes[$box+1][0]);
            unset($this->columns[$column+1][0]);
            unset($this->rows[$row+1][0]);
        }
        // to start at natural sudoku index 1
        unset($this->boxes[0]);
        unset($this->columns[0]);
        unset($this->rows[0]);
    }

    public function getCell($index)
    {
        return $this->grid[$index];
    }

    public function getGrid($only_empty = false)
    {
        if ($only_empty)
        {
            $emptyCells = array();
            foreach ($this->grid as $cell)
            {
                if ($cell->isEmpty())
                {
                    $emptyCells[] = $cell;
                }
            }
            return $emptyCells;
        }
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

    public function getCols()
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

    public function getCol($index)
    {
        return $this->columns[$index];
    }

    /**
     * Return an array of the values of the given cells
     * @param array[cell] $agglo array of cells
     * @return array[int]        Array containing the values of the cells
     */
    public static function getValues($cells)
    {
        $values = array();
        foreach ($cells as $cell)
        {
            $values[] = $cell->getValue();
        }
        return $values;
    }

    /**
     * Finds the buddies of the given cell.
     *
     * A buddy of a cell is a cell that is either in the
     * same row, column of box as the cell
     *
     * @param  Cell   $cell cell for which to find the buddies
     * @return array[Cell]       array of buddies
     */
    public function findBuddies($cell)
    {
        $buddies = array();

        foreach ($this->getRow($cell->row) as $rowCell)
        {
            if ($rowCell != $cell)
            {
                $buddies[] = $rowCell;
            }
        }

        foreach ($this->getCol($cell->col) as $colCell)
        {
            if ($colCell != $cell && !in_array($colCell, $buddies))
            {
                $buddies[] = $colCell;
            }
        }

        foreach ($this->getBox($cell->box) as $boxCell)
        {
            if ($boxCell != $cell && !in_array($boxCell, $buddies))
            {
                $buddies[] = $boxCell;
            }
        }
        $buddies = array_filter($buddies); // remove empty values
        return $buddies;
    }

    /**
     * Check if the grid is solved
     * @return boolean true if the grid is solved, false otherwise
     */
    public function isSolved()
    {
        $cellsProd = 1;
        foreach ($this->grid as $cell)
        {
            // if the grid is not solved, at least one cell has value 0
            $cellsProd *= $cell->getValue();
        }

        return $cellsProd != 0;
    }

    /**
     * returns a string corresponding to the values of the grid.
     *
     * Here are the indexes of the values
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
     *
     * @return string encoding of the grid
     */
    public function encoding()
    {
        $encoding = "";
        foreach (Grid::getValues($this->grid->getBoxes()) as $value)
        {
            $encoding .= $value;
        }
        return $encoding;
    }
}
