<?php

namespace App\Services;
use App\Sudoku\Solvers\HumanLikeSolver;
use App\Http\Resources\SolverResource;
use Illuminate\Http\Request;
use App\Sudoku\Grid;

class SolverService
{
    public function solve(Request $request)
    {
        $grid = new Grid($request->get('grid'));
        $solver = new HumanLikeSolver($grid);
        $found = $solver->gridSolve();
        $solverResource = new SolverResource($solver,$found);
        $solverResource->setReturnFormat($request->get('returnFormat'));
        return $solverResource;
    }
}
