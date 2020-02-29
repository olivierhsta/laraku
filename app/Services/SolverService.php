<?php

namespace App\Services;

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
        InteractionSolver::class,
    ];

    public function solve(String $gridEncoding, $returnFormat = 'box', $solversName = [])
    {
        if (is_null($solversName) || count($solversName) == 0)
        {
            $solversName = $this->solversName;
        }
        else
        {
            $solversName = array_map(function($solver) { return 'App\\Sudoku\\Solvers\\' . $solver;} , $solversName);
            if (count(array_intersect($solversName, $this->solversName)) != count($solversName))
            { // if the $solver parameter passed contains invalid values : default to all solvers
                $solversName = $this->solversName;
            }
        }
        $grid = Solver::prepare(
            new Grid($gridEncoding)
        );
        $found = array();
        $limitCounter = 0;
        do {
            $beforeFound = $found;
            foreach ($solversName as $solverName) {
                $solver = new $solverName($grid);
                $solver->solve();
                $found = array_merge($found, $solver->getFindings());
            }
            $limitCounter++;
        } while (sizeof($beforeFound) != sizeof($found)
                 && $limitCounter < config()->get('sudoku.iterationLimit'));
        $solverResource = new SolverResource($solver,$found);
        $solverResource->setReturnFormat($returnFormat);
        return $solverResource;
    }
}
