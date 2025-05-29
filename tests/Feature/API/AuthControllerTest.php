<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user_returns_returns_values_​​and_types_correctly(): void
    {
        $user = User::factory()->make()->only(['name', 'email', 'password']);

        $response = $this->postJson('api/auth/register', $user);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $json) use ($user){
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'email' => 'string'
            ]);

            $json->whereAll([
                'id' => 1,
                'name' => $user['name'],
                'email' => $user['email']
            ]);
        });
        $response->assertJsonMissing(['password']);
    }

    public function test_register_user_returns_errors_when_body_is_empty(): void
    {
        $response = $this->postJson('api/auth/register');

        $response->assertUnprocessable();
        $response->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]
        ]);
        $response->assertExactJsonStructure([
            'message',
            'errors' => ['name', 'email', 'password']
        ]);
    }

    public function test_register_user_returns_error_when_email_is_invalid(): void
    {
        $user = User::factory()
            ->make(['email' => 'invalid@email.test'])
            ->only(['name', 'email', 'password']);

        $response = $this->postJson('api/auth/register', $user);

        $response->assertUnprocessable();
        $response->assertJson([
            'message' => 'The email field must be a valid email address.'
        ]);
        $response->assertExactJsonStructure([
            'errors' => ['email',],
            'message'
        ]);
    }

    public function test_register_user_returns_error_when_name_length_is_greater_than_50(): void
    {
        $user = User::factory()
            ->make(['name' => fake()->paragraph()])
            ->only(['name', 'email', 'password']);

        $response = $this->postJson('api/auth/register', $user);

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.name.0', 'The name field must not be greater than 50 characters.');
        $response->assertExactJsonStructure([
            'errors' => ['name',],
            'message'
        ]);

    }

    public function test_register_user_returns_error_when_password_lenght_is_less_than_8(): void
    {
        $user = User::factory()
            ->make()
            ->only(['name', 'email', 'password']);

        $user['password'] = '1234567';

        $response = $this->postJson('api/auth/register', $user);

        $response->assertUnprocessable();
        $response->assertJson([
            'message' => 'The password field must be at least 8 characters.'
        ]);
        $response->assertExactJsonStructure([
            'errors' => ['password',],
            'message'
        ]);
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
        $response->assertExactJsonStructure(['auth_token', 'token_type']);

        Sanctum::actingAs($user);
        $response = $this->getJson('api/auth/user');

        $response->assertOk();
        $response->assertExactJson($user->only(['id', 'name', 'email']));
    }

    public function test_login_user_returns_an_error_for_empty_body(): void
    {
        $response = $this->postJson('api/auth/login');

        $response->assertUnprocessable();
        $response->assertJson([
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
        $response->assertExactJsonStructure([
            'errors' => ['email', 'password',],
            'message'
        ]);
    }

    public function test_login_user_returns_an_error_for_invalid_credentials(): void
    {
        $user = User::factory()->make()->only(['email', 'password']);

        $response = $this->postJson('api/auth/login', $user);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Invalid credentials.']);
    }

    public function test_logout_user_was_successful(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post('api/auth/logout');

        $response->assertNoContent();
    }
}
