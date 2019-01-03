<?php

namespace App\Sudoku\Solvers;
use App\Sudoku\Grid;
use InvalidArgumentException, Exception;

abstract class Solver
{
    protected $grid;
    protected $found = array(array());
    public $full_group = [1,2,3,4,5,6,7,8,9];

    public function __construct(Grid &$grid)
    {
        unset($this->found[0]);
        $this->grid = &$grid;
        $this->originalGrid = clone $grid;
    }

    public function get_found_values()
    {
        return $this->found;
    }

    /**
     * Helper function to check if the group name given is valid.
     * Throws InvalidArgumentException if not.
     *
     * Valid names are 'box', 'row', 'col'
     *
     * @param  string $groupName group name to test
     */
    public static function validate_group_name($groupName)
    {
        if ($groupName !== 'col' && $groupName !== 'row' && $groupName !== 'box')
        {
            throw new InvalidArgumentException("group must be either named 'row', 'col' or 'box', given name was : " . $groupName);
        }
    }

    /**
     * Get the solved grid.  This grid may be NOT FULLY filled if the solver was
     * unable to solve the sudoku.
     * @return Grid solved grid
     */
    public function getSolvedGrid()
    {
        return $this->grid;
    }

    /**
     * Get the original grid
     * @return Grid original grid
     */
    public function getOrignalGrid()
    {
        return $this->originalGrid;
    }


    /**
     * Solving algorithm that goes through the whole grid.
     * It solves the class parameter $this->grid.
     *
     * Must be define by subclass.
     *
     */
    public abstract function grid_solve();

    /**
     * Solving algorithm that goes through only one group (box, row or col).
     *
     * Must be define by subclass.
     *
     * @param Cell[] $group Group on which to perform the algorithm.
     */
    public abstract function group_solve(array $group);
}
