<?php

use App\Enums\TaskPriority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $values = implode("', '", TaskPriority::toArray());
            $default = TaskPriority::MEDIUM->value;

            DB::statement("
                ALTER TABLE tasks
                    ALTER COLUMN priority TYPE VARCHAR(255),
                    ALTER COLUMN priority SET NOT NULL,
                    ALTER COLUMN priority SET DEFAULT '{$default}',
                    ALTER COLUMN priority DROP IDENTITY IF EXISTS,
                    ADD CONSTRAINT priority_check CHECK (priority IN ('{$values}'))
            ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            DB::statement('ALTER TABLE tasks DROP CONSTRAINT IF EXISTS priority_check');
        });
    }
};
