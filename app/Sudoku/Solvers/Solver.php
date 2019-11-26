<?php

namespace App\Sudoku\Solvers;

use App\Patterns\Singleton;

use App\Sudoku\Grid;
use App\Sudoku\Sudoku;
use InvalidArgumentException, Exception;

abstract class Solver extends Singleton
{
    static $pencilMarksWritten = false;
    protected $grid;
    protected $found = array(array());

    public function __construct(Grid &$grid)
    {
        unset($this->found[0]);
        $this->grid = &$grid;
        $this->originalGrid = clone $grid;
    }

    /**
     * Get the solved grid.
     * This grid may be NOT FULLY filled if the solver was
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

    public function getFindings()
    {
        return $this->found;
    }


    /**
     * Solving algorithm that goes through the whole grid.
     *
     * Must be define by subclass.
     *
     * @abstract
     */
    public abstract function solve();

    /**
     * Adds an entry to the array of findings.
     *
     * Must be define by subclass.
     *
     * @abstract
     * @param Cell           $cell         cell affected
     * @param array[int]|int $pencilMarks  pencil mark value(s) that were affected
     * @param array          $args         other arguments
     */
    private abstract function markMove($cell, $pencilMarks=null, $args=array());


    /**
    * Helper function to check if the group name given is valid.
    * Throws InvalidArgumentException if not.
    *
    * Valid names are 'box', 'row', 'col'
    *
    * @param  string $groupName group name to test
    */
    public static function validateGroupName($groupName)
    {
        if ($groupName !== 'col' && $groupName !== 'row' && $groupName !== 'box')
        {
            throw new InvalidArgumentException("group must be either named 'row', 'col' or 'box', given name was : " . $groupName);
        }
    }

    /**
     * Prepares the given grid to be solve by the solvers.
     *
     * This function NEEDS to be called before solving, otherwise
     * most of the solver won't be able to perform their
     * respective algorithm.
     *
     * @param  Grid   $grid grid to be prepared
     * @return Grid         prepared grid
     */
    public static function prepare(Grid $grid)
    {
        return self::writePencilMarks($grid);
    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return Grid grid with pencil marks in it
     */
    private static function writePencilMarks(Grid $grid)
    {
        foreach ($grid->getGrid() as $cell)
        {
            if ($cell->isEmpty())
            {
                $buddies = Grid::getValues($cell->getBuddies());
                $cell->setPencilMarks(array_diff(config()->get('sudoku.fullGroup'), $buddies));
            }
        }
        return $grid;
    }

}
