<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Schedule;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random date in June of the current year
        $year = now()->year;
        $start = $this->faker->dateTimeBetween("$year-06-01", "$year-06-30 23:59:59");

        // Clone and add 1 to 8 hours for the end time
        $end = (clone $start)->modify('+' . rand(1, 8) . ' hours');

        return [
            'employee_id' => '111-111',
            'day_type' => $this->faker->randomElement(['working', 'holiday', 'dayoff']),
            'sched_date' => $start->format('Y-m-d'),  // Only the date portion
            'sched_start' => $start,
            'sched_end' => $end,
        ];
    }
}
