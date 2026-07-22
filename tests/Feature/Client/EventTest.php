<?php

namespace Tests\Feature\Client;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'client']);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test client can create an event
     */
    public function test_client_can_create_event()
    {
        $eventType = \App\Models\EventType::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson('/api/client/events', [
                             'name' => 'Birthday Party',
                             'description' => 'A fun birthday celebration',
                             'event_type_id' => $eventType->id,
                             'start_date' => '2026-08-15',
                             'end_date' => '2026-08-15',
                             'location_text' => 'Downtown Hall',
                             'guest_estimate' => 50,
                             'budget_overall' => 5000,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('events', ['name' => 'Birthday Party']);
    }

    /**
     * Test client can view their events
     */
    public function test_client_can_view_their_events()
    {
        Event::factory()->create(['client_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson('/api/client/events');

        $response->assertStatus(200);
    }

    /**
     * Test client can update their event
     */
    public function test_client_can_update_event()
    {
        $event = Event::factory()->create(['client_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->putJson("/api/client/events/{$event->id}", [
                             'name' => 'Updated Party Name',
                             'event_type_id' => $event->event_type_id,
                             'start_date' => $event->start_date,
                             'location_text' => $event->location_text,
                             'guest_estimate' => $event->guest_estimate,
                             'budget_overall' => 6000,
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', ['name' => 'Updated Party Name']);
    }

    /**
     * Test client can delete their event
     */
    public function test_client_can_delete_event()
    {
        $event = Event::factory()->create(['client_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->deleteJson("/api/client/events/{$event->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /**
     * Test client cannot access other client's events
     */
    public function test_client_cannot_view_other_clients_events()
    {
        $otherUser = User::factory()->create(['role' => 'client']);
        $event = Event::factory()->create(['client_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson("/api/client/events/{$event->id}");

        // Should be either 403 (forbidden) or 404 (not found)
        $this->assertContains($response->status(), [403, 404]);
    }
}
