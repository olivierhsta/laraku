<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

class HumanLikeSolver extends Solver
{
    /**
     * list of subsolver classes to perform in the complete algorithm.
     * @var string[]
     */
    private $subSolversName = [
        'App\Sudoku\Solvers\OneChoiceSolver',
        'App\Sudoku\Solvers\EliminationSolver',
        'App\Sudoku\Solvers\NakedSubsetSolver',
    ];
    private $subSolvers = [];

    public function __construct(Grid &$grid)
    {
        Solver::__construct($grid);
        $test = new OneChoiceSolver($grid);
        foreach ($this->subSolversName as $SubSolver) {
            $this->subSolvers[] = new $SubSolver($grid);
        }
    }

    /**
     * Entry point for the solving algorithm
     * @return Grid       Instante of Sudoku\Grid reprensenting the solved form of the sudoku (if solvable).
     */
    public function grid_solve()
    {
        $this->write_pencil_marks();
        $found = array();
        $limit_counter = 0;
        do {
            $before_found = $found;
            foreach($this->subSolvers as $subSolver)
            {
                $localFound = $subSolver->grid_solve();
                if (is_array($localFound) && !empty($localFound))
                {
                    $found = array_merge($found, $localFound);
                }
            }
            $limit_counter++;
        } while (sizeof($before_found) != sizeof($found)
                 && $limit_counter < 100);
        return $found;
    }

    public function group_solve(array $group)
    {

    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return null
     */
    public function write_pencil_marks()
    {
        $one_nine = [1,2,3,4,5,6,7,8,9];
        foreach ($this->grid->get_grid() as $cell)
        {
            if ($cell->is_empty())
            {
                $buddies = Grid::get_values($cell->get_buddies());
                $cell->set_pencil_marks(array_diff($one_nine, $buddies));
            }
        }
    }
}
