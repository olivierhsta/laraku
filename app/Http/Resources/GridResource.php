<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GridResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $encoding = [];
        foreach ($this->resource as $group)
        {
            foreach ($group as $cell)
            {
                $encoding[] = $cell->isEmpty() ? $cell->getPencilMarks() : $cell->getValue();
            }
        }
        return $encoding;
    }

    public function toString()
    {
        $stringEncoding = "";
        foreach ($this->resource as $group)
        {
            foreach ($group as $cell)
            {
                $stringEncoding .= $cell->isEmpty() ? '0' : $cell->getValue();
            }
        }
        return $stringEncoding;
    }
}
