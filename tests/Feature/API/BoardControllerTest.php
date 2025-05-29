<?php

namespace Tests\Feature\API;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_board_returns_ordered_statuse_and_related_ordered_tasks(): void
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

        $response = $this->getJson('api/board');

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
                        'status' => ['id' => $todoStatus->id],
                        'order' => 1
                    ],
                    [
                        'key' => 'TASK-4',
                        'status' => ['id' => $todoStatus->id],
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
                        'status' => ['id' => $inProgressStatus->id],
                        'order' => 0
                    ],
                    [
                        'key' => 'TASK-1',
                        'status' => ['id' => $inProgressStatus->id],
                        'order' => 2
                    ]
                ]
            ]
        ]);
    }
}
