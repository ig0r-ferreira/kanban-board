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
            'name' => 'Backlog',
            'slug' => 'backlog',
        ]);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'slug' => 'string',

            ])->etc();
            $json->whereAll([
                'id' => 1,
                'name' => 'Backlog',
                'slug' => 'backlog'
            ]);
        });
    }

    public function test_store_status_returns_errors_when_body_is_empty(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson('api/status');

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.name.0', 'The name field is required.');
        $response->assertJsonPath('errors.slug.0', 'The slug field is required.');
    }

    public function test_store_status_returns_error_when_not_sauthenticated(): void
    {
        $response = $this->postJson('/api/status', [
            'name' => 'Backlog',
            'slug' => 'backlog'
        ]);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_store_status_returns_error_when_name_length_is_greater_than_30(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $status = Status::factory()->withNameLengthGreaterThan30()->make();
        
        $response = $this->postJson('api/status', $status->toArray());

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.name.0', 'The name field must not be greater than 30 characters.');
    }

    public function test_store_status_returns_error_when_slug_length_is_greater_than_30(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $status = Status::factory()->withSlugLengthGreaterThan30()->make();

        $response = $this->postJson('api/status', $status->toArray());

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.slug.0', 'The slug field must not be greater than 30 characters.');
    }

    public function test_store_status_returns_error_when_name_is_not_unique(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $status = Status::factory()->create();
        
        $response = $this->postJson('api/status', $status->toArray());

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.name.0', 'The name has already been taken.');
    }

    public function test_store_status_returns_error_when_slug_is_not_unique(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $status = Status::factory()->create();
        
        $response = $this->postJson('api/status', $status->toArray());

        $response->assertUnprocessable();
        $response->assertJsonPath('errors.slug.0', 'The slug has already been taken.');
    }
}
