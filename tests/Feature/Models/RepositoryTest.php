<?php

namespace Tests\Feature\Models;

use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $baseUrl;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = '/api/repositories';
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(401);
    }

    public function test_returns_200_for_authentication()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->getJson($this->baseUrl);


        // 403 is a policy violation.
        $response->assertStatus(200);
    }

    public function test_returns_repositories_for_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            'team_id' => $user->currentTeam->id,
        ]);

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200);

        $response->assertJsonPath('data.0.id', $repo->id);
    }
}
