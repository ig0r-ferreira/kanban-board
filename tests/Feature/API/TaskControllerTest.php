<?php

namespace Tests\Feature\API;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_returns_values_​​and_types_correctly(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $task = Task::factory()->make()->only([
            'title',
            'description',
            'status_id',
            'priority',
            'reporter_id',
            'assignee_id',
            'due_date'
        ]);
        
        $response = $this->postJson('/api/task', $task);

        $response->assertCreated();
        $response->assertJson($task);
        $response->assertJsonPath('key', 'TASK-1');
        $response->assertJsonPath('resolution_date', null);
    }
}
