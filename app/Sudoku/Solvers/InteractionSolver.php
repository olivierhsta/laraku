<?php

namespace App\Sudoku\Solvers;

use App\Sudoku\Grid;

class InteractionSolver extends Solver
{
    public function __construct(Grid &$grid) {
        Solver::__construct($grid);
    }

    /**
     * Executes the interaction algorithm on the whole grid.
     *
     * It goes first over every box of the grid and for each box,
     * it checks if there is any pencil mark that is only in any
     * one row/column.  If there is suck pencil mark, remove it from
     * all other cell in the row/column.
     *
     * It then performs the same logic with the rows, and then the columns.
     * The only difference is that for rows and columns, there is only
     * one other group type that needs to be check (boxes) instead of two.
     */
    public function solve() {
        foreach ($this->grid->getBoxes() as $boxIndex => $box)
        {
            for ($pencilMark=1; $pencilMark <= 9; $pencilMark++)
            {
                $pencilMarksRows = array();
                $pencilMarksCols = array();
                foreach ($box as $cell)
                {
                    if($cell->isEmpty() && in_array($pencilMark, $cell->getPencilMarks()))
                    {
                        $pencilMarksRows[] = $cell->row();
                        $pencilMarksCols[] = $cell->col();
                    }
                }
                if (count(array_unique($pencilMarksRows)) === 1)
                // all the pencil marks are on the same row
                {
                    foreach ($this->grid->getRow($pencilMarksRows[0]) as $cell)
                    {
                        if ($cell->isEmpty() && $cell->box() != $boxIndex)
                        {
                            $cell->removePencilMarks($pencilMark);
                            $this->found[] = [
                                "cell" => $cell->row() . $cell->col(),
                                "method" => "Interaction",
                                "action" => "Remove Pencil Marks",
                                "values" => array($pencilMark),
                                "grid" => $this->grid->encoding(),
                            ];
                        }
                    }
                }
                if (count(array_unique($pencilMarksCols)) === 1)
                // all the pencil marks are on the same column
                {
                    foreach ($this->grid->getCol($pencilMarksCols[0]) as $cell)
                    {
                        if ($cell->isEmpty() && $cell->box() != $boxIndex)
                        {
                            $cell->removePencilMarks($pencilMark);
                            $this->found[] = [
                                "cell" => $cell->row() . $cell->col(),
                                "method" => "Interaction",
                                "action" => "Remove Pencil Marks",
                                "values" => array($pencilMark),
                                "grid" => $this->grid->encoding(),
                            ];
                        }
                    }
                }
            }
        }

        foreach ($this->grid->getRows() as $rowIndex => $row)
        {
            for ($pencilMark=1; $pencilMark <= 9; $pencilMark++)
            {
                $pencilMarksBoxes = array();
                foreach ($row as $cell)
                {
                    if($cell->isEmpty() && in_array($pencilMark, $cell->getPencilMarks()))
                    {
                        $pencilMarksBoxes[] = $cell->box();
                    }
                }
                if (count(array_unique($pencilMarksBoxes)) === 1)
                // all the pencil marks are in the same box
                {
                    foreach ($this->grid->getBox($pencilMarksBoxes[0]) as $cell)
                    {
                        if ($cell->isEmpty() && $cell->row() != $rowIndex)
                        {
                            $cell->removePencilMarks($pencilMark);
                            $this->found[] = [
                                "cell" => $cell->row() . $cell->col(),
                                "method" => "Interaction",
                                "action" => "Remove Pencil Marks",
                                "values" => array($pencilMark),
                                "grid" => $this->grid->encoding(),
                            ];
                        }
                    }
                }
            }
        }

        foreach ($this->grid->getCols() as $colIndex => $col)
        {
            for ($pencilMark=1; $pencilMark <= 9; $pencilMark++)
            {
                $pencilMarksBoxes = array();
                foreach ($col as $cell)
                {
                    if($cell->isEmpty() && in_array($pencilMark, $cell->getPencilMarks()))
                    {
                        $pencilMarksBoxes[] = $cell->box();
                    }
                }
                if (count(array_unique($pencilMarksBoxes)) === 1)
                // all the pencil marks are in the same box
                {
                    foreach ($this->grid->getBox($pencilMarksBoxes[0]) as $cell)
                    {
                        if ($cell->isEmpty() && $cell->col() != $colIndex)
                        {
                            $cell->removePencilMarks($pencilMark);
                            $this->found[] = [
                                "cell" => $cell->row() . $cell->col(),
                                "method" => "Interaction",
                                "action" => "Remove Pencil Marks",
                                "values" => array($pencilMark),
                                "grid" => $this->grid->encoding(),
                            ];
                        }
                    }
                }
            }
        }
    }
}
