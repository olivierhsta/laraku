<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Resources\SolverResource;
use App\Sudoku\Grid;
use App\Sudoku\Solvers\Solver;
use App\Sudoku\Solvers\OneChoiceSolver;
use App\Sudoku\Solvers\EliminationSolver;
use App\Sudoku\Solvers\NakedSubsetSolver;
use App\Sudoku\Solvers\InteractionSolver;

class SolverService
{
    /**
     * list of solver classes to perform in the complete algorithm.
     * @var string[]
     */
    private $solversName = [
        OneChoiceSolver::class,
        EliminationSolver::class,
        NakedSubsetSolver::class,
        // InteractionSolver::class,
    ];

    public function solve(Request $request)
    {
        $grid = new Grid($request->get('grid'));
        $grid = Solver::prepare($grid);
        $found = array();
        $limitCounter = 0;
        do {
            $beforeFound = $found;
            foreach ($this->solversName as $solverName) {
                $solver = new $solverName($grid);
                $solver->solve();
                $found = array_merge($found, $solver->getFindings());
            }
            $limitCounter++;
        } while (sizeof($beforeFound) != sizeof($found)
                 && $limitCounter < config()->get('sudoku.iterationLimit'));
        $solverResource = new SolverResource($solver,$found);
        $solverResource->setReturnFormat($request->get('returnFormat'));
        return $solverResource;
    }
}
