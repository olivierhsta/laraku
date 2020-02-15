<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Services\SolverService;
use App\Http\Resources\GridResource;

class OneChoiceSolverTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSolve()
    {
        $response = $this->postJson('/api/solver', [
            'grid' => '200070540364050000000006003007059001800000005300710900700600000000070621028050007',
            'returnFormat' => 'box'
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'solved_grid' => [2,9,8,1,7,3,5,4,6,3,6,4,9,5,2,1,8,7,5,7,1,8,4,6,2,9,3,4,2,7,8,5,9,3,6,1,8,1,9,2,3,6,7,4,5,3,6,5,7,1,4,9,8,2,7,1,4,6,3,2,9,8,5,5,9,3,4,7,8,6,2,1,6,2,8,1,5,9,4,3,7]
            ]);
    }
}
