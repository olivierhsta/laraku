<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Sudoku\Cell;

class SolverResource extends JsonResource
{
    private $returnFormat = 'box';
    protected $acceptedFormat = ['row', 'col', 'box'];

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
                $return['solved_grid'] = $this->groupEncode(
                    $this->resource->getSolvedGrid()->getRows()
                );
                break;
            case 'col':
                $return['format'] = 'col';
                $return['solved_grid'] = $this->groupEncode(
                    $this->resource->getSolvedGrid()->getCols()
                );
                break;
            case 'box':
            default:
                $return['format'] = 'box';
                $return['solved_grid'] = $this->groupEncode(
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

    private function groupEncode(array $grid)
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
