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
    // app/Database/Factories/ScheduleFactory.php

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('2025-08-01 10:00:00', '2025-10-30 07:00:00');
        $end = (clone $start)->modify('+8 hours');

        return [
            'employee_id' => '111-111',
            'day_type' => $this->faker->randomElement(['working', 'holiday', 'dayoff']),
            'sched_date' => $start->format('Y-m-d'),
            'sched_start' => $start,
            'sched_end' => $end,
        ];
    }
}
