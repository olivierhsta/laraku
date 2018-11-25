<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SudokuController extends Controller
{
    public function solver(){
        return view('sudoku.solver');
    }
}
