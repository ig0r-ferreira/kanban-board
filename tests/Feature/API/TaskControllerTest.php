<?php

namespace Tests\Feature\API;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_task_returns_values_​​and_types_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $status = Status::factory()->create(['name' => 'Backlog']);
        $task = Task::factory()->make()->except(
            ['created_at', 'updated_at']
        );

        $response = $this->postJson('/api/task', $task);

        $response->assertCreated();
        $response->assertJson($task);
        $response->assertJsonPath('key', 'TASK-1');
        $response->assertJsonPath('status_id', $status->id);
        $response->assertJsonPath('resolution_date', null);
    }

    public function test_store_task_returns_error_when_status_not_exists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make()->except(
            ['created_at', 'updated_at']
        );

        $response = $this->postJson('/api/task', $task);

        $response->assertServerError();
        $response->assertJsonPath('message', "The 'Backlog' status is not exist.");
    }

    public function test_store_task_returns_error_when_title_length_is_greater_than_50(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'title' => fake()->sentences(6, true)
        ]);

        $response = $this->postJson('api/task', $task->toArray());

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The title field must not be greater than 50 characters.',
            'errors' => [
                'title' => ['The title field must not be greater than 50 characters.']
            ]
        ]);
    }

    public function test_store_task_returns_error_when_selected_priority_is_invalid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'priority' => fake()->word()
        ]);

        $response = $this->postJson('api/task', $task->toArray());

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The selected priority is invalid.',
            'errors' => [
                'priority' => ['The selected priority is invalid.']
            ]
        ]);
    }

    public function test_store_task_returns_error_when_reporter_not_exists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'reporter_id' => -1
        ]);

        $response = $this->postJson('api/task', $task->toArray());

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The selected reporter id is invalid.',
            'errors' => [
                'reporter_id' => ['The selected reporter id is invalid.']
            ]
        ]);
    }

    public function test_store_task_returns_error_when_assignee_not_exists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'assignee_id' => -1
        ]);

        $response = $this->postJson('api/task', $task->toArray());

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The selected assignee id is invalid.',
            'errors' => [
                'assignee_id' => ['The selected assignee id is invalid.']
            ]
        ]);
    }

    public function test_store_task_returns_error_when_due_date_is_less_than_today(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'due_date' => date('Y-m-d', strtotime("-1 days"))

        ]);

        $response = $this->postJson('api/task', $task->toArray());

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The due date field must be a date after or equal to today.',
            'errors' => [
                'due_date' => ['The due date field must be a date after or equal to today.']
            ]
        ]);
    }
}
