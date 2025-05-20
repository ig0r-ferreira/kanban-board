<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $backlogStatus = Status::create(['name' => 'Backlog']);
        Status::create(['name' => 'In progress']);
        Status::create(['name' => 'Finished']);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => '12345678'
        ]);

        Task::factory(5)->create([
            'status_id' => $backlogStatus->id,
            'assignee_id' => $user->id,
            'reporter_id' => $user->id
        ]);
    }
}
