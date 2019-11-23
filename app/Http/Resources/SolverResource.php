<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Sudoku\Cell;

class SolverResource extends JsonResource
{
    private $returnFormat = 'box';
    protected $acceptedFormat = ['row', 'col', 'box'];
    private $found;

    public function __construct($resource, $found)
    {
        JsonResource::__construct($resource);
        $this->found = $found;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch ($this->format) {
            case 'row':
                $return['format'] = 'row';
                $return['solved_grid'] = $this->encode(
                    $this->resource->getSolvedGrid()->getRows()
                );
                $return['found'] = $this->found;
                break;
            case 'col':
                $return['format'] = 'col';
                $return['solved_grid'] = $this->encode(
                    $this->resource->getSolvedGrid()->getCols()
                );
                break;
            case 'box':
            default:
                $return['format'] = 'box';
                $return['solved_grid'] = $this->encode(
                    $this->resource->getSolvedGrid()->getBoxes()
                );
                break;
        }
        return $return;
    }

    public function setReturnFormat($format)
    {
        $format = strtolower($format);
        if(in_array($format, $this->acceptedFormat))
        {
            $this->format = $format;
        }
    }

    private function encode(array $grid)
    {
        $encoding = [];
        foreach ($grid as $group)
        {
            foreach ($group as $cell)
            {
                $encoding[] = $cell->isEmpty() ? $cell->getPencilMarks() : $cell->getValue();
            }
        }
        return $encoding;
    }
}
