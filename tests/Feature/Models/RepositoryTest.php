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

        $response->assertJsonPath('0.id', $repo->id);
    }

    public function test_returns_404_for_repsoitory_show_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => 123456
        ]);

        $response = $this->getJson($this->baseUrl . '/' . $repo->id);

        $response->assertStatus(404);
    }

    public function test_returns_404_for_repository_update_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => 123456
        ]);

        $response = $this->putJson($this->baseUrl . '/' . $repo->id, ['name' => 'foo']);

        $response->assertStatus(404);
    }
    
    public function test_returns_success_for_repository_update_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => $user->currentTeam->id,
        ]);

        $response = $this->putJson($this->baseUrl . '/' . $repo->id, ['name' => 'foo']);

        $response->assertSuccessful();
    }

    public function test_returns_404_for_repsoitory_delete_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => 123456
        ]);

        $response = $this->deleteJson($this->baseUrl . '/' . $repo->id);

        $response->assertStatus(404);
    }
    
    public function test_returns_success_for_repsoitory_delete_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => $user->currentTeam->id,
        ]);

        $response = $this->deleteJson($this->baseUrl . '/' . $repo->id);

        $response->assertSuccessful();
    }

    public function test_returns_422_for_repsoitory_create_on_other_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $repo = Repository::factory()->create([
            // Note, this team doesn't exist.
            'team_id' => 123456
        ]);

        $response = $this->postJson($this->baseUrl, []);

        $response->assertStatus(422);
    }
}
