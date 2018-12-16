<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sudoku\Solver;
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
        $solver = new Solver($grid);
        $solution = $solver->solve();


        return view('sudoku.solver')->with([
                'grid' => $solution
        ]);
    }
}
