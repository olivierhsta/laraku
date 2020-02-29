<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NakedSubsetSolverTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->postJson('/api/solver', [
            'grid' => '465798123080032560320605098800002570205000306030500080084000057053020010170854063',
            'returnFormat' => 'box',
            'solvers' => ['NakedSubsetSolver']
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    'format'=>'box',
                    'solved_grid' => [4,6,5,7,9,8,1,2,3,[1,7,9],8,[1,7,9],[1,4],3,2,5,6,[4,7],3,2,[1,7],6,[1],5,[4,7],9,8,8,[1,4],[1,6,9],[3,6,9],[1,3],2,5,7,[1,9],2,[4,7,9],5,[1,8],[7,9],[1,8],3,[4,9],6,[7,9],3,[1,6,7,9],5,4,[1,6,7,9],[2,9],8,[1,2,9],[2,6,9],8,4,[3,6,9],[1,3],[1,6,9],[2,9],5,7,[6,9],5,3,[6,7,9],2,[7,9],[4,8,9],1,[4,8,9],1,7,[2,9],8,5,4,[2,9],6,3]
                ]
            ]);
    }
}
