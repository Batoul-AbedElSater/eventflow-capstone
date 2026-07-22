<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration with valid data
     */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password@123',
            'role' => 'client',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'token', 'user' => ['id', 'name', 'email', 'role', 'phone']]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test registration fails with invalid email
     */
    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'role' => 'client',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration fails when phone is missing
     */
    public function test_registration_fails_when_phone_missing()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password@123',
            'role' => 'client',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test registration fails when email already exists
     */
    public function test_registration_fails_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'role' => 'client',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test user can login with correct credentials
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'token', 'user' => ['id', 'name', 'email']])
                 ->assertJson(['success' => true]);
    }

    /**
     * Test login fails with wrong password
     */
    public function test_login_fails_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('CorrectPassword@123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'WrongPassword@123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['success' => false, 'message' => 'Invalid credentials']);
    }

    /**
     * Test login fails with non-existent email
     */
    public function test_login_fails_with_non_existent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['success' => false, 'message' => 'Invalid credentials']);
    }

    /**
     * Test authenticated user can logout
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        // Create a token for the user
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'message' => 'Logged out successfully']);
    }

    /**
     * Test unauthenticated user cannot access protected endpoint
     */
    public function test_unauthenticated_user_cannot_access_protected_endpoint()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /**
     * Test user can get their profile after login
     */
    public function test_user_can_get_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success'])
                 ->assertJsonFragment(['id' => $user->id, 'email' => $user->email]);
    }
}
