<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = '1-1';
        $year = now()->year;

        for ($day = 1; $day <= 30; $day++) {
            $date = Carbon::create($year, 6, $day);
            $start = (clone $date)->setTime(rand(8, 10), rand(0, 59));
            $end = (clone $start)->addHours(8);

            Schedule::create([
                'employee_id' => $employeeId,
                'day_type' => fake()->randomElement(['working', 'holiday', 'dayoff']),
                'sched_date' => $date->format('Y-m-d'),
                'sched_start' => $start,
                'sched_end' => $end,
            ]);
        }
    }
}
