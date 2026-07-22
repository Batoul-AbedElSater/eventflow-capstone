<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\PlannerProfile;
use App\Models\StaffProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation with valid data
     */
    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'client',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    /**
     * Test user has name and email
     */
    public function test_user_has_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /**
     * Test user can create API token
     */
    public function test_user_can_create_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $this->assertNotNull($token->plainTextToken);
    }

    /**
     * Test user with client role
     */
    public function test_user_can_have_client_role()
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->assertEquals('client', $user->role);
    }

    /**
     * Test user with planner role
     */
    public function test_user_can_have_planner_role()
    {
        $user = User::factory()->create(['role' => 'planner']);

        $this->assertEquals('planner', $user->role);
    }

    /**
     * Test user with assistant role
     */
    public function test_user_can_have_assistant_role()
    {
        $user = User::factory()->create(['role' => 'assistant']);

        $this->assertEquals('assistant', $user->role);
    }

    /**
     * Test user email is unique
     */
    public function test_user_email_must_be_unique()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'duplicate@example.com']);
    }

    /**
     * Test user password is hashed
     */
    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create();

        $this->assertNotEquals('password', $user->password);
    }
}
