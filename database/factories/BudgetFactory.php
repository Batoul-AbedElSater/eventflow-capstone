<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'planner_id' => User::factory(),
            'total_client_budget' => $this->faker->numberBetween(1000, 50000),
            'planner_fee' => $this->faker->numberBetween(100, 5000),
            'total_assistant_fees' => $this->faker->numberBetween(100, 3000),
            'estimated_total' => $this->faker->numberBetween(1500, 55000),
            'status' => 'draft',
            'planner_notes' => $this->faker->paragraph(),
        ];
    }
}
