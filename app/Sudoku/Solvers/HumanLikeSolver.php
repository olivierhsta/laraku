<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

class HumanLikeSolver extends Solver
{
    /**
     * list of subsolver classes to perform in the complete algorithm.
     * @var string[]
     */
    private $subSolversName = [
        'App\Sudoku\Solvers\OneChoiceSolver',
        'App\Sudoku\Solvers\EliminationSolver',
        'App\Sudoku\Solvers\NakedSubsetSolver',
    ];
    private $subSolvers = [];

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
        $test = new OneChoiceSolver($grid);
        foreach ($this->subSolversName as $SubSolver) {
            $this->subSolvers[] = new $SubSolver($grid);
        }
    }

    /**
     * Entry point for the solving algorithm
     * @return Grid       Instante of Sudoku\Grid reprensenting the solved form of the sudoku (if solvable).
     */
    public function gridSolve()
    {
        $this->writePencilMarks();
        $found = array();
        $limitCounter = 0;
        do {
            $beforeFound = $found;
            foreach($this->subSolvers as $subSolver)
            {
                $localFound = $subSolver->gridSolve();
                if (is_array($localFound) && !empty($localFound))
                {
                    $found = array_merge($found, $localFound);
                }
            }
            $limitCounter++;
        } while (sizeof($beforeFound) != sizeof($found)
                 && $limitCounter < 100);
        return $found;
    }

    public function groupSolve(array $group)
    {

    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return null
     */
    public function writePencilMarks()
    {
        $oneToNine = [1,2,3,4,5,6,7,8,9];
        foreach ($this->grid->getGrid() as $cell)
        {
            if ($cell->isEmpty())
            {
                $buddies = Grid::getValues($cell->getBuddies());
                $cell->setPencilMarks(array_diff($oneToNine, $buddies));
            }
        }
    }
}
