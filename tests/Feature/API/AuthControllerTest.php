<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

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

    public function test_login_user_returns_a_valid_token(): void
    {
        $password = '12345678';
        $user = User::factory()->create([
            'password' => $password
        ]);
        
        $response = $this->postJson(
            'api/auth/login', [
                'email' => $user->email,
                'password' => $password
            ]
        );

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => 
            $json->hasAll(['auth_token', 'token_type'])
        );

        $response = $this->withHeaders([
            'Authorization' => "$response[token_type] $response[auth_token]"
        ])->getJson('api/user');

        $response->assertOk();
        $response->assertJson($user->toArray());
    }

    public function test_login_user_returns_an_error_for_invalid_credentials(): void
    {
        $user = User::factory()->make();
        
        $response = $this->postJson(
            'api/auth/login', [
                'email' => $user->email,
                'password' => $user->password
            ]
        );

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Invalid credentials.']);
    }
}
