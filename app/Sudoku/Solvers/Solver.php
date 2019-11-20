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
        $this->writePencilMarks();
    }

    /**
     * Writes the pencil marks for every cell of the grid
     * @return null
     */
    public function writePencilMarks()
    {
        if (!self::$pencilMarksWritten)
        {
            foreach ($this->grid->getGrid() as $cell)
            {
                if ($cell->isEmpty())
                {
                    $buddies = Grid::getValues($cell->getBuddies());
                    $cell->setPencilMarks(array_diff(config()->get('sudoku.fullGroup'), $buddies));
                }
            }
            self::$pencilMarksWritten = true;
        }
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
     * It solves the class parameter $this->grid.
     *
     * Must be define by subclass.
     *
     * @abstract
     */
    public abstract function solve();


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

}
