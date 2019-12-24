<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Sudoku\Grid;
use App\Sudoku\Solvers\Solver;
use App\Http\Resources\GridResource;
use App\Sudoku\Solvers\OneChoiceSolver;

class OneChoiceSolverTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSolve()
    {
        $grid = Solver::prepare(
            new Grid('200070540364050000000006003007059001800000005300710900700600000000070621028050007')
        );

        $found = array();
        $limitCounter = 0;
        do {
            $beforeFound = $found;
            $solver = new OneChoiceSolver($grid);
            $solver->solve();
            $found = array_merge($found, $solver->getFindings());
            $limitCounter++;
        } while (sizeof($beforeFound) != sizeof($found)
                 && $limitCounter < config()->get('sudoku.iterationLimit'));

        $expectedSolvedGrid = new Grid('298173546364952187571846293427859361819236745365714982714632985593478621628159437');
        $this->assertEquals(
            (new GridResource($solver->getSolvedGrid()->getBoxes()))->toString(),
            (new GridResource($expectedSolvedGrid->getBoxes()))->toString()
        );
    }
}
