<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user_returns_a_successful_response(): void
    {
        $user = User::factory()->make();

        $response = $this->postJson(
            'api/auth/register', $user->only(['name', 'email', 'password'])
        );

        $response->assertStatus(201);
        $response->assertJson([
            'id' => 1,
            'name' => $user->name,
            'email' => $user->email
        ]);
        $response->assertJsonMissing(['password']);
    }
}
