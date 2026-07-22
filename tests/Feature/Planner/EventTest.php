<?php

namespace Tests\Feature\Planner;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected $planner;
    protected $token;
    protected $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->planner = User::factory()->create(['role' => 'planner']);
        $this->token = $this->planner->createToken('test-token')->plainTextToken;
        $this->event = Event::factory()->create();
    }

    /**
     * Test planner can view events endpoint
     */
    public function test_planner_can_view_events()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/planner/events');

        $response->assertStatus(200);
    }

    /**
     * Test planner dashboard endpoint
     */
    public function test_planner_can_access_dashboard()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/planner/dashboard');

        $response->assertStatus(200);
    }
}

