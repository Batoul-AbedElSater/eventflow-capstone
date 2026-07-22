<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_type_id' => EventType::factory(),
            'client_id' => User::factory()->create(['role' => 'client'])->id,
            'start_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'end_date' => $this->faker->dateTimeBetween('+30 days', '+60 days'),
            'location_text' => $this->faker->address(),
            'guest_estimate' => $this->faker->numberBetween(10, 500),
            'budget_overall' => $this->faker->numberBetween(1000, 50000),
            'status' => 'draft',
        ];
    }
}
