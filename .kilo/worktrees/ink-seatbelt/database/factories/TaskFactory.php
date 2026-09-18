<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_list_id' => function () {
                return TaskList::inRandomOrder()->first()?->id ?? TaskList::create([
                    'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
                    'name' => 'General Tasks',
                    'description' => 'Default task list',
                ])->id;
            },
            'created_by' => User::factory(),
            'assigned_to' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'status' => fake()->randomElement(['Pending', 'In Progress', 'Completed']),
            'deadline' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
        ];
    }
}
