<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => 'todo',
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
