<?php

namespace App\Sudoku\Solvers;

use InvalidArgumentException, Exception;
use App\Sudoku\Grid;
use App\Sudoku\Sudoku;
use App\Sudoku\Solvers\OneChoiceSolver;
use App\Sudoku\Solvers\EliminationSolver;
use App\Sudoku\Solvers\NakedSubsetSolver;
use App\Sudoku\Solvers\InteractionSolver;

class HumanLikeSolver extends Solver
{
    /**
     * list of subsolver classes to perform in the complete algorithm.
     * Absolute path must be given.
     * @var string[]
     */
    private $subSolversName = [
        OneChoiceSolver::class,
        EliminationSolver::class,
        NakedSubsetSolver::class,
        InteractionSolver::class,
    ];
    private $subSolvers = [];

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
        foreach ($this->subSolversName as $SubSolver) {
            $this->subSolvers[] = new $SubSolver($grid);
        }
    }

    /**
     * Goes through all of the human-like strategy and executes them in the
     * given order.  If there was no progress during the process (aka. no value
     * found and no pencil mark removed), we stop.  Otherwise we continue until
     * we find nothing or we reach the iteration limit $HUMAN_LIKE_ITERATION_LIMIT
     *
     * @return array step-by-step resolution of the grid.
     */
    public function solve() {
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
                 && $limitCounter < config()->get('sudoku.humanLikeIterationLimit'));
        return $found;
    }

    public function groupSolve(array $group) {

    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return null
     */
    public function writePencilMarks()
    {
        foreach ($this->grid->getGrid() as $cell)
        {
            if ($cell->isEmpty())
            {
                $buddies = Grid::getValues($cell->getBuddies());
                $cell->setPencilMarks(array_diff(config()->get('sudoku.fullGroup'), $buddies));
            }
        }
    }
}
