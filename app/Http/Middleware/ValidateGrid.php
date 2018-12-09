<?php

namespace App\Http\Middleware;

use Closure;

class ValidateGrid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $grid = $request->grid;
        for( $i=0 ; $i < strlen($grid) ; $i++ )
        {
            $cell_value = (int)$grid[$i];

            if ( (string)$cell_value != $grid[$i] )
            {
                return back()->withErrors('The grid must only contain integers');
            }
        }

        return $next($request);
    }
}
