<?php

namespace Database\Seeders;

use App\Models\Leadman\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [
            ['name' => 'De-leafing', 'description' => '-'],
            ['name' => 'De-flower', 'description' => '-'],
            ['name' => 'De-finger', 'description' => '-'],
            ['name' => 'Bagging', 'description' => '-'],
            ['name' => 'Bud Injection', 'description' => '-'],
            ['name' => 'Ground and Weed Spray', 'description' => '-'],
            ['name' => 'Fertilizing', 'description' => '-'],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
