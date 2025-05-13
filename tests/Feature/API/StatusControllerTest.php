<?php

namespace Tests\Feature\API;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_status_returns_values_​​and_types_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/status', [
            'name' => 'Backlog'
        ]);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string'

            ])->etc();
            $json->whereAll([
                'id' => 1,
                'name' => 'Backlog'
            ]);
        });
    }

    public function test_store_status_returns_errors_when_body_is_empty(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('api/status');

        $response->assertUnprocessable();
        $response->assertExactJson([
            'message' => 'The name field is required.',
            'errors' => ['name' => ['The name field is required.']]
        ]);
    }

    public function test_store_status_returns_error_when_not_sauthenticated(): void
    {
        $response = $this->postJson('/api/status', [
            'name' => 'Backlog'
        ]);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_store_status_returns_error_when_name_length_is_greater_than_30(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $status = Status::factory()->make([
            'name' => fake()->sentences(4, true)
        ])->toArray();

        $response = $this->postJson('api/status', $status);

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The name field must not be greater than 30 characters.',
            'errors' => [
                'name' => ['The name field must not be greater than 30 characters.']
            ]
        ]);
    }

    public function test_store_status_returns_error_when_name_is_not_unique(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $status = Status::factory()->create()->toArray();

        $response = $this->postJson('api/status', $status);

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The name has already been taken.',
            'errors' => [
                'name' => ['The name has already been taken.']
            ]
        ]);
    }
}
