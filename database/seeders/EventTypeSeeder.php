<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Wedding', 'description' => 'Wedding ceremonies and receptions'],
            ['name' => 'Birthday Party', 'description' => 'Birthday celebrations'],
            ['name' => 'Corporate Event', 'description' => 'Business events and conferences'],
            ['name' => 'Anniversary', 'description' => 'Anniversary celebrations'],
            ['name' => 'Baby Shower', 'description' => 'Baby shower parties'],
            ['name' => 'Graduation', 'description' => 'Graduation celebrations'],
            ['name' => 'Social Gathering', 'description' => 'Social parties and gatherings'],
        ];

        foreach ($types as $type) {
            EventType::create($type);
        }
    }
}
