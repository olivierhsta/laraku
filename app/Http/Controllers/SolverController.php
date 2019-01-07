<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sudoku\Solvers\HumanLikeSolver;
use App\Sudoku\Grid;

class SolverController extends Controller
{
    public function index()
    {
        return view('sudoku.solver');
    }

    public function solve()
    {
        $this->validate(request(), ['grid' => 'required|min:81|max:81']);

        $grid = new Grid(request('grid'));
        $solver = new HumanLikeSolver($grid);
        $found = $solver->gridSolve();

        return view('sudoku.solver')->with([
                'grid' => $solver->getSolvedGrid()->getRows(),
                'found' => $found
        ]);
    }
}
