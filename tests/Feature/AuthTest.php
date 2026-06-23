<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure a user can authenticate successfully and receive a Sanctum bearer token.
     * Garantiza que un usuario pueda autenticarse correctamente y recibir un token Bearer de Sanctum.
     */
    public function test_user_can_log_in_and_receive_a_bearer_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
            ])
            ->assertJson([
                'token_type' => 'Bearer',
            ]);
    }

    /**
     * Ensure the login endpoint rejects requests with invalid credentials.
     * Garantiza que el endpoint de login rechace peticiones con credenciales no validas.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }
}