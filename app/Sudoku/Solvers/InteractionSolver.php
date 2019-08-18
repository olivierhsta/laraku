<?php

namespace App\Sudoku\Solvers;

use App\Sudoku\Grid;

class InteractionSolver extends Solver
{
    public function __construct(Grid &$grid) {
        Solver::__construct($grid);
    }

    public function gridSolve() {
        $foundRow = $this->interactionBy('row');
        $foundCol = $this->interactionBy('col');
        $foundBox = $this->interactionBy('box');
        return array_merge($foundRow, $foundCol, $foundBox);
    }

    public function groupSolve(array $group) {
        $found = array();

        $cellsPerNumber = [0,0,0,0,0,0,0,0,0];

        for ($i=1; $i <= 9; $i++) {
            $cell = $group[i];
            $pencilMarks = $cell->getPencilMarks();
            foreach ($pencilMarks as $pencilMark) {
                $cellsPerNumber[$pencilMark]++;
            }
            if ($i % 3 == 0) {
                $cellsPerNumber = [0,0,0,0,0,0,0,0,0];
            }
        }
        return $found;
    }

    public function interactrionBy($groupName) {
        Solver::validateGroupName($groupName);

        $found = array();

        $getter = 'get'.ucfirst($groupName).($groupName == 'box' ? 'es' : 's');
        // uses of variable-variable. becomes either
        // $this->grid->getCols(), getRows() or getBoxes()
        foreach ($this->grid->$getter() as $group)
        {
            $found = $this->groupSolve($group);
        }
        return $found;
    }
}
