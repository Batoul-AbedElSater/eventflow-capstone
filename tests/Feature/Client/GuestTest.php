<?php

namespace Tests\Feature\Client;

use App\Models\User;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'client']);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->event = Event::factory()->create(['client_id' => $this->user->id]);
    }

    /**
     * Test client can add guest to event
     */
    public function test_client_can_add_guest()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson("/api/client/events/{$this->event->id}/guests", [
                             'name' => 'John Guest',
                             'email' => 'john.guest@example.com',
                             'phone' => '+1234567890',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('guests', ['email' => 'john.guest@example.com']);
    }

    /**
     * Test client can view guests
     */
    public function test_client_can_view_guests()
    {
        Guest::factory()->create(['event_id' => $this->event->id]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->getJson("/api/client/events/{$this->event->id}/guests");

        $response->assertStatus(200);
    }

    /**
     * Test guest email is required
     */
    public function test_guest_email_is_required()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
                         ->postJson("/api/client/events/{$this->event->id}/guests", [
                             'name' => 'John Guest',
                             'phone' => '+1234567890',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
