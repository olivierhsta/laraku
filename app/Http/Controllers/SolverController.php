<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sudoku\Solvers\HumanLikeSolver;
use App\Services\SolverService;
use App\Sudoku\Grid;

class SolverController extends Controller
{

    public function index() {
        return view('sudoku.solver');
    }

    public function solve(SolverService $solverService) {
        $this->validate(request(), ['grid' => 'required|min:81|max:81']);
        return $solverService->solve(request());
    }
}
