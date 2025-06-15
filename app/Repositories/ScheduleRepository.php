<?php

namespace App\Repositories;

use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ScheduleRepository
{
    public function getLastFiveSchedule(string $employeeID, string $timezone = 'UTC')
    {
        try {
            $localToday = Carbon::now($timezone)->toDateString();
            // return Schedule::with('dtr')->with('user')
            //     ->where('employee_id', $employeeID)
            //     ->where('sched_date', '<=', $localToday)
            //     ->orderByDesc('sched_date')
            //     ->orderByDesc('start_time')
            //     ->limit(5)
            //     ->get();
            $schedules = Schedule::with('user')
                ->where('employee_id', $employeeID)
                ->where('sched_date', '<=', $localToday)
                ->orderByDesc('sched_date')
                ->orderByDesc('start_time')
                ->limit(5)
                ->get();

            // Attach the correct DTR based on employee_id + time_in = sched_date
            $schedules->each(function ($schedule) {
                $schedule->setRelation('dtr', $schedule->dtr()->whereDate('time_in', $schedule->sched_date)->first());
            });

            return $schedules;
        } catch (Exception $e) {
            Log::error("Failed to fetch schedules for employee: {$employeeID}. Error: " . $e->getMessage());
            return collect();
        }
    }
}
