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

        $response = $this->postJson('/api/tasks', $task);

        $response->assertCreated();
        $response->assertJson($task);
        $response->assertJsonPath('key', 'TASK-1');
        $response->assertJsonPath('status_id', $status->id);
        $response->assertJsonPath('resolution_date', null);
        $response->assertJsonPath('order', 0);
    }

    public function test_store_task_returns_order_attribute_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $status = Status::factory()->create(['name' => 'Backlog']);
        $task = Task::factory()->make(['order' => 5])->except(
            ['created_at', 'updated_at']
        );

        $response = $this->postJson('/api/tasks', $task);

        $response->assertCreated();
        $response->assertJson($task);
        $response->assertJsonPath('key', 'TASK-1');
        $response->assertJsonPath('status_id', $status->id);
        $response->assertJsonPath('resolution_date', null);
        $response->assertJsonPath('order', 5);
    }


    public function test_store_task_returns_error_when_status_not_exists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make()->except(
            ['created_at', 'updated_at']
        );

        $response = $this->postJson('/api/tasks', $task);

        $response->assertServerError();
        $response->assertJsonPath('message', "The 'Backlog' status is not exist.");
    }

    public function test_store_task_returns_error_when_title_length_is_greater_than_50(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $task = Task::factory()->make([
            'title' => fake()->sentences(6, true)
        ])->toArray();

        $response = $this->postJson('api/tasks', $task);

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
        ])->toArray();

        $response = $this->postJson('api/tasks', $task);

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
        ])->toArray();

        $response = $this->postJson('api/tasks', $task);

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
        ])->toArray();

        $response = $this->postJson('api/tasks', $task);

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

        ])->toArray();

        $response = $this->postJson('api/tasks', $task);

        $response->assertUnprocessable();
        $response->assertExactJson([
            "message" => 'The due date field must be a date after or equal to today.',
            'errors' => [
                'due_date' => ['The due date field must be a date after or equal to today.']
            ]
        ]);
    }

    public function test_get_task_returns_all_tasks_successfully()
    {
        Sanctum::actingAs(User::factory()->create());

        $total = 3;
        $status = Status::factory()->create(['name' => 'Backlog']);
        $tasks = Task::factory()
            ->count($total)
            ->create(['status_id' => $status->id])
            ->toArray();

        $response = $this->getJson('api/tasks');

        $response->assertOk();
        $response->assertJsonCount($total);
        $response->assertJsonIsArray();
        $response->assertJson($tasks);
    }

    public function test_get_task_returns_empty_array_when_there_are_no_tasks()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('api/tasks');

        $response->assertOk();
        $response->assertJsonCount(0);
        $response->assertJsonIsArray();
        $response->assertJson([]);
    }

    public function test_edit_task_returns_changed_data_correctly()
    {
        Sanctum::actingAs(User::factory()->create());

        $backlogStatus = Status::factory()->create(['name' => 'Backlog']);
        $inProgressStatus = Status::factory()->create(['name' => 'In progress']);
        $task = Task::factory()->create(['status_id' => $backlogStatus->id]);

        $response = $this->patchJson('api/tasks/' . $task->id, [
            'title' => 'Changed Title',
            'status_id' => $inProgressStatus->id
        ]);

        $response->assertOk();
        $response->assertJson([
           'key' => $task->key,
           'title' => 'Changed Title',
           'status_id' => $inProgressStatus->id
        ]);
    }
}
