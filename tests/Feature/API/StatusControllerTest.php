<?php

namespace Tests\Feature\API;

use App\Models\Status;
use App\Models\Task;
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
            'name' => 'Test',
        ]);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'order' => 'integer'

            ])->etc();
            $json->whereAll([
                'id' => 1,
                'name' => 'Test',
                'order' => 0
            ]);
        });
    }

    public function test_store_status_returns_order_attribute_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/status', [
            'name' => 'Test',
            'order' => 5
        ]);

        $response->assertCreated();
        $response->assertJson([
            'name' => 'Test',
            'order' => 5
        ]);
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

    public function test_get_status_returns_ordered_statuse_and_related_ordered_tasks(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $inProgressStatus = Status::factory()
            ->create(['name' => 'In Progress', 'order' => 1]);
        $todoStatus = Status::factory()->create(['name' => 'To Do']);

        Task::factory()->create([
            'status_id' => $inProgressStatus->id, 'order' => 2
        ]);
        Task::factory()->create([
            'status_id' => $inProgressStatus->id
        ]);
        Task::factory()->create([
            'status_id' => $todoStatus->id, 'order' => 1
        ]);
        Task::factory()->create([
            'status_id' => $todoStatus->id, 'order' => 3
        ]);

        $response = $this->getJson('api/status');

        $response->assertOk();
        $response->assertJsonIsArray();
        $response->assertJson([
           [
                'id' => $todoStatus->id,
                'name' => 'To Do',
                'order' => 0,
                'tasks' => [
                    [
                        'key' => 'TASK-3',
                        'status_id' => $todoStatus->id,
                        'order' => 1
                    ],
                    [
                        'key' => 'TASK-4',
                        'status_id' => $todoStatus->id,
                        'order' => 3
                    ]
                ]
            ],
            [
                'id' => $inProgressStatus->id,
                'name' => 'In Progress',
                'order' => 1,
                'tasks' => [
                    [
                        'key' => 'TASK-2',
                        'status_id' => $inProgressStatus->id,
                        'order' => 0
                    ],
                    [
                        'key' => 'TASK-1',
                        'status_id' => $inProgressStatus->id,
                        'order' => 2
                    ]
                ]
            ]
        ]);
    }
}
