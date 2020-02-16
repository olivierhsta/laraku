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
            'returnFormat' => 'box',
            'solvers' => ['OneChoiceSolver']
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    'format'=>'box',
                    'solved_grid' => str_split('298173546364952187571846293427859361819236745365714982714632985593478621628159437')
                ]
            ]);
    }
}
