<?php

namespace Tests\Feature\API;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_status_returns_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/statuses', [
            'name' => 'Test',
        ]);

        $response->assertCreated();
        $response->assertJson(['name' => 'Test']);
        $response->assertExactJsonStructure(['id', 'name']);
    }

    public function test_store_status_returns_errors_when_body_is_empty(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('api/statuses');

        $response->assertUnprocessable();
        $response->assertExactJson([
            'message' => 'The name field is required.',
            'errors' => ['name' => ['The name field is required.']]
        ]);
    }

    public function test_store_status_returns_error_when_not_sauthenticated(): void
    {
        $response = $this->postJson('/api/statuses', [
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

        $response = $this->postJson('api/statuses', $status);

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

        $response = $this->postJson('api/statuses', $status);

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The name has already been taken.',
            'errors' => [
                'name' => ['The name has already been taken.']
            ]
        ]);
    }

    public function test_get_statuses_returns_all_tasks_successfully()
    {
        Sanctum::actingAs(User::factory()->create());

        $total = 5;
        $statuses = Status::factory()
            ->count($total)
            ->create()
            ->map(fn($status) => $status->only(['id', 'name']))
            ->toArray();

        $response = $this->getJson('api/statuses');

        $response->assertOk();
        $response->assertJsonCount($total);
        $response->assertJsonIsArray();
        $response->assertExactJson($statuses);
    }

    public function test_get_statuses_returns_empty_array_when_there_are_no_tasks()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('api/statuses');

        $response->assertOk();
        $response->assertJsonCount(0);
        $response->assertJsonIsArray();
        $response->assertExactJson([]);
    }
}
