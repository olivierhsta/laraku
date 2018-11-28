<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solver;

class SolverController extends Controller
{

    public function index()
    {
        return view('sudoku.solver');
    }

    public function solve()
    {
        $solver = new Solver();
        $this->validate(request(), ['grid' => 'required|min:81|max:81']);
        $solution = $solver->solve(request('grid'));

        return view('sudoku.solver')->with(
            'solution', $solution
        );
    }
}
