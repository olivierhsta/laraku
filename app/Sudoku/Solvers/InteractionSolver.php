<?php

namespace App\Sudoku\Solvers;

use App\Sudoku\Grid;

class InteractionSolver extends Solver
{
    public function __construct(Grid &$grid) {
        Solver::__construct($grid);
    }

    public function solve() {
        $pencilMarksRows = array();
        $pencilMarksCols = array();
        $rowAffected = null;
        $colAffected = null;

        foreach ($this->grid->getBoxes() as $group)
        {
            $box = $group[1]->box();
            for ($i=1; $i <= 9; $i++)
            {
                $pencilMark = $i;
                foreach ($group as $cell)
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
                        if ($cell->isEmpty() && $cell->box() != $box)
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
                        if ($cell->isEmpty() && $cell->row() != $box)
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

    public function groupSolve(array $group) {
        $rows = array();
        $cols = array();

        foreach ($group as $i => $cell) {
            $rows[] = $cell->getRow();
            $cols[] = $cell->getCol();
        }
        $rows = array_unique($rows);
        $cols = array_unique($cols);
    }

    public function interactrionBy($groupName) {
        Solver::validateGroupName($groupName);

        $getter = 'get'.ucfirst($groupName).($groupName == 'box' ? 'es' : 's');
        // uses of variable-variable. becomes either
        // $this->grid->getCols(), getRows() or getBoxes()
        foreach ($this->grid->$getter() as $group)
        {
            $this->groupSolve($group);
        }
    }
}
